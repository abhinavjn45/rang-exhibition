<?php
    function get_site_option($option_name, $skip_replacement = false) {
        global $con;
        
        $stmt = mysqli_prepare($con, "SELECT option_value FROM site_options WHERE option_key = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $option_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        if (!$row) {
            return null;
        }
        
        $value = $row['option_value'];
        
        // Skip replacement for nested options to prevent infinite recursion
        if ($skip_replacement) {
            return $value;
        }
        
        // Fetch nested options first (with skip_replacement=true to prevent recursion)
        $site_fullname = get_site_option('site_fullname', true);
        $site_title = get_site_option('site_title', true);
        $site_url = get_site_option('site_url', true);
        $admin_email = get_site_option('admin_email', true);
        
        // Replace placeholders
        $replacements = [
            '{year}' => date('Y'),
            '{month}' => date('F'),
            '{date}' => date('Y-m-d'),
            '{time}' => date('H:i:s'),
            '{day}' => date('d'),
            '{site_fullname}' => $site_fullname,
            '{site_title}' => $site_title,
            '{site_url}' => $site_url,
            '{admin_email}' => $admin_email,
            '{current_user}' => isset($_SESSION['member_email']) ? $_SESSION['member_email'] : 'Guest'
        ];
        
        foreach ($replacements as $placeholder => $replacement) {
            if ($replacement !== null) {
                $value = str_replace($placeholder, $replacement, $value);
            }
        }
        
        return $value;
    }

    function get_hero_section_data($hero_content_key) {
        global $con;
        
        $stmt = mysqli_prepare($con, "SELECT hero_content_value FROM hero_section WHERE hero_content_key = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $hero_content_key);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        if (!$row) {
            return null;
        }
        
        $value = $row['hero_content_value'];
        
        // Fetch nested options first (with skip_replacement=true to prevent recursion)
        $site_fullname = get_site_option('site_fullname', true);
        $site_title = get_site_option('site_title', true);
        $site_url = get_site_option('site_url', true);
        $admin_email = get_site_option('admin_email', true);
        
        // Replace placeholders
        $replacements = [
            '{year}' => date('Y'),
            '{month}' => date('F'),
            '{date}' => date('Y-m-d'),
            '{time}' => date('H:i:s'),
            '{day}' => date('d'),
            '{site_fullname}' => $site_fullname,
            '{site_title}' => $site_title,
            '{site_url}' => $site_url,
            '{admin_email}' => $admin_email,
            '{current_user}' => isset($_SESSION['member_email']) ? $_SESSION['member_email'] : 'Guest',
            '{office_phone_number}' => "+91 76889 95560"
        ];
        
        foreach ($replacements as $placeholder => $replacement) {
            if ($replacement !== null) {
                $value = str_replace($placeholder, $replacement, $value);
            }
        }
        
        return $value;
    }

    function get_all_events($display_type = null, $status = null, $order_by = null, $sorting = null, $limit = null) {
        global $con;

        $current_date_time = date('Y-m-d H:i:s');
        
        if ($display_type === 'upcoming-events') {
            if ($order_by === null) {
                $order_by = 'event_id';
            }

            if ($sorting === null) {
                $sorting = 'DESC';
            }

            $query = " SELECT e.*, c.*, v.*, pkg.lowest_price FROM events e
                LEFT JOIN cities c ON e.event_city = c.city_id
                LEFT JOIN venues v ON e.event_venue = v.venue_id 
                LEFT JOIN (
                    SELECT package_for_event, MIN(CAST(package_price AS DECIMAL(10,2))) AS lowest_price
                    FROM packages
                    WHERE package_status = 'active'
                    GROUP BY package_for_event
                ) pkg ON pkg.package_for_event = e.event_id
                WHERE event_status NOT IN ('completed', 'deleted') 
                AND (c.city_status = 'active' OR c.city_status IS NULL)
                AND (v.venue_status = 'active' OR v.venue_status IS NULL)
                ORDER BY $order_by $sorting 
            ";

            if ($limit !== null && is_numeric($limit)) {
                $query .= " LIMIT ?";
            }

            $stmt = mysqli_prepare($con, $query);

            if ($limit !== NULL && is_numeric($limit)) {
                mysqli_stmt_bind_param($stmt, 'i', $limit);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) <= 0) {
                echo '<p class="text-muted">No upcoming events found.</p>';
                return;
            }

            while ($event = mysqli_fetch_assoc($result)) {
                $event_id = $event['event_id'];
                $event_thumbnail = htmlspecialchars($event['event_thumbnail']);

                // Initialize event badge to prevent undefined variable warning
                $event_badge = "";

                // Check if slots are defined (not NULL or empty)
                $has_slots_data = !empty($event['event_stall_slots']) && isset($event['event_stall_slots_left']);
                $low_threshold = $has_slots_data ? ($event['event_stall_slots'] * 0.1) : 0;

                // Priority 1: Check slot status
                if ($has_slots_data && $event['event_stall_slots_left'] <= 0) {
                    // Sold Out
                    $event_badge = "
                        <div class='event-badge badge-soldout'>
                            Sold Out
                        </div>
                    ";
                } elseif ($has_slots_data && $event['event_stall_slots_left'] <= $low_threshold && $event['event_stall_slots_left'] > 0) {
                    // Filling Fast
                    $event_badge = "
                        <div class='event-badge badge-filling-fast'>
                            Filling Fast
                        </div>
                    ";
                } else {
                    // Priority 2: Check date-based status
                    $has_from = !empty($event['event_from']);
                    $has_to = !empty($event['event_to']);
                    
                    if ($has_from && $has_to) {
                        $event_from = date("Y-m-d H:i:s", strtotime($event['event_from']));
                        $event_to = date("Y-m-d H:i:s", strtotime($event['event_to']));
                        
                        // Completed: both dates are in the past
                        if ($event_to < $current_date_time) {
                            $event_badge = "
                                <div class='event-badge badge-completed'>
                                    Completed
                                </div>
                            ";
                        }
                        // Live: event is currently happening
                        elseif ($event_from <= $current_date_time && $event_to >= $current_date_time) {
                            $event_badge = "
                                <div class='event-badge badge-ongoing'>
                                    Live
                                </div>
                            ";
                        }
                        // Upcoming: event hasn't started yet
                        elseif ($event_from > $current_date_time) {
                            $event_badge = "
                                <div class='event-badge badge-upcoming'>
                                    Upcoming
                                </div>
                            ";
                        }
                    } else {
                        // No dates: check if event is new
                        if ($event['event_added_on'] >= date('Y-m-d H:i:s', strtotime('-3 days'))) {
                            $event_badge = "
                                <div class='event-badge badge-new'>
                                    New
                                </div>
                            ";
                        }
                    }
                }

                $event_name = htmlspecialchars($event['event_name']);
                if (!empty($event['venue_name'])) {
                    $event_venue_name = htmlspecialchars($event['venue_name']);
                } else {
                    $event_venue_name = "To Be Announced";
                }

                $event_lowest_package = isset($event['lowest_price']) ? htmlspecialchars($event['lowest_price']) : null;
                    if ($event_lowest_package !== NULL || !empty($event_lowest_package)) {
                        $price_display = "₹ " . number_format($event_lowest_package) . " Onwards";
                    } else {
                        $price_display = "Pricing To Be Announced";
                    }

                // Determine slots display text
                $event_stall_slots_left = htmlspecialchars($event['event_stall_slots_left']);

                // Truncate venue label for card width; PHP-only per requirement
                $event_venue_display = $event_venue_name;
                $venue_label_limit = 55;
                if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                    if (mb_strlen($event_venue_display) > $venue_label_limit) {
                        $event_venue_display = mb_substr($event_venue_display, 0, $venue_label_limit - 3) . '...';
                    }
                } else {
                    if (strlen($event_venue_display) > $venue_label_limit) {
                        $event_venue_display = substr($event_venue_display, 0, $venue_label_limit - 3) . '...';
                    }
                }
                
                // Smart date formatting
                $from_timestamp = strtotime($event['event_from']);
                $to_timestamp = strtotime($event['event_to']);
                $from_month = date("m", $from_timestamp);
                $to_month = date("m", $to_timestamp);
                $from_year = date("Y", $from_timestamp);
                $to_year = date("Y", $to_timestamp);
                if (empty($event['event_from']) && empty($event['event_to'])) {
                    $event_date_display = "To Be Announced";
                } elseif ($from_month === $to_month && $from_year === $to_year) {
                    // Same month and year: "17 - 18 Jan, 2026"
                    $event_date_display = date("d", $from_timestamp) . " - " . date("d M, Y", $to_timestamp);
                } elseif ($from_year === $to_year) {
                    // Same year, different months: "30 Jan - 2 Feb, 2026"
                    $event_date_display = date("d M", $from_timestamp) . " - " . date("d M, Y", $to_timestamp);
                } else {
                    // Different years: "30 Dec, 2025 - 2 Jan, 2026"
                    $event_date_display = date("d M, Y", $from_timestamp) . " - " . date("d M, Y", $to_timestamp);
                }

                $event_stall_slots_left = htmlspecialchars($event['event_stall_slots_left']);
                    if (empty($event_stall_slots_left) || $event_stall_slots_left === NULL) {
                        $stalls_left_display = "All Slots Available!";
                    } else {
                        $stalls_left_display = "Only <span class='event-slots-number'>$event_stall_slots_left Slots</span> left!";
                    }
                $event_form_link = htmlspecialchars($event['event_form_link']);
                $event_slug = htmlspecialchars($event['event_slug']);

                echo "
                    <div class='event-card'>
                        <div class='event-thumbnail'>
                            <img src='" . get_site_option('dashboard_url') ."assets/uploads/images/events/$event_thumbnail' alt='$event_name' class='event-thumbnail-img'>
                            $event_badge
                        </div>
                        <div class='event-content'>
                            <h3 class='event-name'>
                                $event_name
                            </h3>
                            <p class='event-venue' style='max-width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:block;'>
                                <i class='fas fa-map-marker-alt'></i> $event_venue_display
                            </p>
                            <p class='event-venue'>
                                <i class='fas fa-calendar-alt'></i> $event_date_display
                            </p>
                            <p class='event-venue'>
                                <i class='fas fa-tag'></i> $price_display
                            </p>
                            <div class='event-slots'>
                                <i class='fas fa-fire'></i> 
                                <span class='event-slots-text'>
                                    $stalls_left_display
                                </span>
                            </div>
                            <div class='event-actions'>
                                <a href='$event_form_link' target='_blank' class='btn-event-book'>Book Now</a>
                                <a href='" . get_site_option('site_url') . "exhibition-details/?exhibition=$event_slug' class='btn-event-visit'>Details</a>
                            </div>
                        </div>
                    </div>
                ";
            }
        }

        if ($display_type === 'cities-page') {
            if ($order_by === null) {
                $order_by = 'event_id';
            }

            if ($sorting === null) {
                $sorting = 'DESC';
            }

            $query = " SELECT e.*, c.*, v.* FROM events e
                LEFT JOIN cities c ON e.event_city = c.city_id
                LEFT JOIN venues v ON e.event_venue = v.venue_id 
                WHERE event_status != 'deleted' 
                AND (c.city_status = 'active' OR c.city_status IS NULL)
                AND (v.venue_status = 'active' OR v.venue_status IS NULL)
                ORDER BY $order_by $sorting 
            ";

            if ($limit !== null && is_numeric($limit)) {
                $query .= " LIMIT ?";
            }

            $stmt = mysqli_prepare($con, $query);

            if ($limit !== NULL && is_numeric($limit)) {
                mysqli_stmt_bind_param($stmt, 'i', $limit);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) <= 0) {
                echo '<p class="text-muted">No upcoming events found.</p>';
                return;
            }

            while ($event = mysqli_fetch_assoc($result)) {
                $event_id = $event['event_id'];
                $event_thumbnail = htmlspecialchars($event['event_thumbnail']);
                $event_from = date("Y-m-d H:i:s", strtotime($event['event_from']));
                $event_to = date("Y-m-d H:i:s", strtotime($event['event_to']));

                // Initialize event badge to prevent undefined variable warning
                $event_badge = "";

                // Check if slots are defined (not NULL or empty)
                $has_slots_data = !empty($event['event_stall_slots']) && isset($event['event_stall_slots_left']);
                $low_threshold = $has_slots_data ? ($event['event_stall_slots'] * 0.2) : 0;

                // Priority 1: Check slot status
                if ($has_slots_data && $event['event_stall_slots_left'] <= 0) {
                    // Sold Out
                    $event_badge = "
                        <div class='event-badge badge-soldout'>
                            Sold Out
                        </div>
                    ";
                } elseif ($has_slots_data && $event['event_stall_slots_left'] <= $low_threshold && $event['event_stall_slots_left'] > 0) {
                    // Filling Fast
                    $event_badge = "
                        <div class='event-badge badge-filling-fast'>
                            Filling Fast
                        </div>
                    ";
                } else {
                    // Priority 2: Check date-based status
                    $has_from = !empty($event['event_from']);
                    $has_to = !empty($event['event_to']);
                    
                    if ($has_from && $has_to) {
                        $event_from = date("Y-m-d H:i:s", strtotime($event['event_from']));
                        $event_to = date("Y-m-d H:i:s", strtotime($event['event_to']));
                        
                        // Completed: both dates are in the past
                        if ($event_to < $current_date_time) {
                            $event_badge = "
                                <div class='event-badge badge-completed'>
                                    Completed
                                </div>
                            ";
                        }
                        // Live: event is currently happening
                        elseif ($event_from <= $current_date_time && $event_to >= $current_date_time) {
                            $event_badge = "
                                <div class='event-badge badge-ongoing'>
                                    Live
                                </div>
                            ";
                        }
                        // Upcoming: event hasn't started yet
                        elseif ($event_from > $current_date_time) {
                            $event_badge = "
                                <div class='event-badge badge-upcoming'>
                                    Upcoming
                                </div>
                            ";
                        }
                    } else {
                        // No dates: check if event is new
                        if ($event['event_added_on'] >= date('Y-m-d H:i:s', strtotime('-3 days'))) {
                            $event_badge = "
                                <div class='event-badge badge-new'>
                                    New
                                </div>
                            ";
                        }
                    }
                }

                $event_name = htmlspecialchars($event['event_name']);
                if (!empty($event['venue_name'])) {
                    $event_venue_name = htmlspecialchars($event['venue_name']);
                } else {
                    $event_venue_name = "To Be Announced";
                }

                // Truncate venue label for card width; PHP-only per requirement
                $event_venue_display = $event_venue_name;
                $venue_label_limit = 55;
                if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                    if (mb_strlen($event_venue_display) > $venue_label_limit) {
                        $event_venue_display = mb_substr($event_venue_display, 0, $venue_label_limit - 3) . '...';
                    }
                } else {
                    if (strlen($event_venue_display) > $venue_label_limit) {
                        $event_venue_display = substr($event_venue_display, 0, $venue_label_limit - 3) . '...';
                    }
                }
                
                // Smart date formatting
                $from_timestamp = strtotime($event['event_from']);
                $to_timestamp = strtotime($event['event_to']);
                $from_month = date("m", $from_timestamp);
                $to_month = date("m", $to_timestamp);
                $from_year = date("Y", $from_timestamp);
                $to_year = date("Y", $to_timestamp);
                if (empty($event['event_from']) && empty($event['event_to'])) {
                    $event_date_display = "To Be Announced";
                } elseif ($from_month === $to_month && $from_year === $to_year) {
                    // Same month and year: "17 - 18 Jan, 2026"
                    $event_date_display = date("d", $from_timestamp) . " - " . date("d M, Y", $to_timestamp);
                } elseif ($from_year === $to_year) {
                    // Same year, different months: "30 Jan - 2 Feb, 2026"
                    $event_date_display = date("d M", $from_timestamp) . " - " . date("d M, Y", $to_timestamp);
                } else {
                    // Different years: "30 Dec, 2025 - 2 Jan, 2026"
                    $event_date_display = date("d M, Y", $from_timestamp) . " - " . date("d M, Y", $to_timestamp);
                }

                $event_stall_slots_left = htmlspecialchars($event['event_stall_slots_left']);
                    if (empty($event_stall_slots_left) || $event_stall_slots_left === NULL) {
                        $stalls_left_display = "All Slots Available!";
                    } else {
                        $stalls_left_display = "Only <span class='event-slots-number'>$event_stall_slots_left Slots</span> left!";
                    }
                $event_form_link = htmlspecialchars($event['event_form_link']);
                $event_slug = htmlspecialchars($event['event_slug']);

                echo "
                    <div class='event-card'>
                        <div class='event-thumbnail'>
                            <img src='" . get_site_option('dashboard_url') ."assets/uploads/images/events/$event_thumbnail' alt='$event_name' class='event-thumbnail-img'>
                            $event_badge
                        </div>
                        <div class='event-content'>
                            <h3 class='event-name'>
                                $event_name
                            </h3>
                            <p class='event-venue' style='max-width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:block;'>
                                <i class='fas fa-map-marker-alt'></i> $event_venue_display
                            </p>
                            <p class='event-venue'>
                                <i class='fas fa-calendar-alt'></i> $event_date_display
                            </p>
                            <div class='event-slots'>
                                <i class='fas fa-fire'></i> 
                                <span class='event-slots-text'>
                                    $stalls_left_display
                                </span>
                            </div>
                            <div class='event-actions'>
                                <a href='$event_form_link' target='_blank' class='btn-event-book'>Book Now</a>
                                <a href='" . get_site_option('site_url') . "exhibition-details/?exhibition=$event_slug' class='btn-event-visit'>Details</a>
                            </div>
                        </div>
                    </div>
                ";
            }
        }
    }

    function get_featured_events($display_type = null) {
        global $con;

        if ($display_type === 'homepage') {
            $seen_ids = [];
            // 1. Finding the nearest upcoming date
            $stmt_min_date = mysqli_prepare($con, "
                SELECT MIN(event_from) AS min_date FROM events
                WHERE event_status = 'active'
                AND event_from >= NOW()
            ");

            mysqli_stmt_execute($stmt_min_date);
            $result_min_date = mysqli_stmt_get_result($stmt_min_date);
            $min_date = null;
            if ($result_min_date && ($row = mysqli_fetch_assoc($result_min_date)) && !empty($row['min_date'])) {
                $min_date = $row['min_date'];
            }
            mysqli_stmt_close($stmt_min_date);

            // 1. a) Fetch all events happening on the nearest upcoming date
            if ($min_date !== null) {
                $stmt_nearest_events = mysqli_prepare($con, "
                    SELECT e.*, c.*, v.* FROM events e
                    LEFT JOIN cities c ON e.event_city = c.city_id
                    LEFT JOIN venues v ON e.event_venue = v.venue_id
                    WHERE event_status = 'active'
                    AND event_from = ?
                    ORDER BY event_id DESC
                ");

                mysqli_stmt_bind_param($stmt_nearest_events, 's', $min_date);
                mysqli_stmt_execute($stmt_nearest_events);
                $result_nearest_events = mysqli_stmt_get_result($stmt_nearest_events);

                if (mysqli_num_rows($result_nearest_events) <= 0) {
                    echo '<option class="text-muted">No featured events found.</option>';
                    return;
                }

                while ($event = mysqli_fetch_assoc($result_nearest_events)) {
                    $event_id = $event['event_id'];
                    $seen_ids[$event_id] = true;
                    // Get First 3 Images for this City
                    $stmt_city_image = mysqli_prepare($con, "
                        SELECT * FROM images
                        WHERE image_for_content = 'cities'
                        AND image_for_content_id = ?
                        AND image_status = 'active'
                        ORDER BY image_id DESC
                        LIMIT 0, 3
                    ");

                    mysqli_stmt_bind_param($stmt_city_image, 'i', $event['event_city']);
                    mysqli_stmt_execute($stmt_city_image);
                    $result_city_image = mysqli_stmt_get_result($stmt_city_image);

                    $city_images = [];

                    while ($image_row = mysqli_fetch_assoc($result_city_image)) {
                        $city_images[] = $image_row['image_name'];
                    }

                    if (count($city_images) <= 0) {
                        // If No Images found, use thumbnail for all three images
                        $city_images = [
                            $event['event_thumbnail'],
                            $event['event_thumbnail'],
                            $event['event_thumbnail']
                        ];
                    }
                    mysqli_stmt_close($stmt_city_image);

                    $city_name = htmlspecialchars($event['city_name']);
                    $event_name = htmlspecialchars($event['event_name']);
                    $venue_name = !empty($event['venue_name']) ? htmlspecialchars($event['venue_name']) : "To Be Announced";
                    
                    // Smart date formatting
                    $from_timestamp = strtotime($event['event_from']);
                    $to_timestamp = strtotime($event['event_to']);
                    $from_month = date("m", $from_timestamp);
                    $to_month = date("m", $to_timestamp);
                    $from_year = date("Y", $from_timestamp);
                    $to_year = date("Y", $to_timestamp);
                    if (empty($event['event_from']) && empty($event['event_to'])) {
                        $event_date_display = "To Be Announced";
                    } elseif ($from_month === $to_month && $from_year === $to_year) {
                        // Same month and year: "17 - 18 Jan, 2026"
                        $event_date_display = date("M d", $from_timestamp) . " - " . date("d, Y", $to_timestamp);
                    } elseif ($from_year === $to_year) {
                        // Same year, different months: "30 Jan - 2 Feb, 2026"
                        $event_date_display = date("M d", $from_timestamp) . " - " . date("M d, Y", $to_timestamp);
                    } else {
                        // Different years: "30 Dec, 2025 - 2 Jan, 2026"
                        $event_date_display = date("M d, Y", $from_timestamp) . " - " . date("M d, Y", $to_timestamp);
                    }

                    $event_form_link = htmlspecialchars($event['event_form_link']);
                    $event_description = nl2br(htmlspecialchars($event['event_description']));

                    $js_date = date("F d, Y", strtotime($event['event_from']));

                    echo "
                        <article class='featured-slide'>
                            <div class='featured-event-container'>
                                <!-- Left Side: City Visual Experience -->
                                <div class='city-visual-wrapper'>
                                    <div class='city-image-grid'>
                                        <!-- Main Large Image -->
                                        <div class='city-image-item'>
                                            <img src='" . get_site_option('dashboard_url') . "assets/uploads/images/cities/" . $city_images[0] . "' alt='Mumbai skyline and culture' class='city-image-bg'>
                                        </div>
                                        <!-- Top Right Image -->
                                        <div class='city-image-item'>
                                            <img src='" . get_site_option('dashboard_url') . "assets/uploads/images/cities/" . $city_images[1] . "' alt='Lifestyle exhibition crowd' class='city-image-bg'>
                                        </div>
                                        <!-- Bottom Right Image -->
                                        <div class='city-image-item'>
                                            <img src='" . get_site_option('dashboard_url') . "assets/uploads/images/cities/" . $city_images[2] . "' alt='Premium venue interior' class='city-image-bg'>
                                        </div>
                                    </div>
                                    
                                    <!-- City Title Overlay -->
                                    <div class='city-title-overlay'>
                                        <h2>$city_name</h2>
                                        <div class='city-descriptor'>
                                            Luxury • Energy • Scale
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Side: Event Details -->
                                <div class='event-details-wrapper'>
                                    <h1 class='event-title'>$event_name</h1>
                                    <div class='event-location'>
                                        <i class='fas fa-map-marker-alt'></i>$venue_name
                                    </div>
                                    <div class='event-date'>
                                        <i class='fas fa-calendar-alt'></i>$event_date_display
                                    </div>
                                    <div class='countdown-timer' id='countdownTimer'>
                                        <div class='timer-unit'>
                                            <div class='timer-value' id='days$event_id'>00</div>
                                            <div class='timer-label'>Days</div>
                                        </div>
                                        <div class='timer-separator'>:</div>
                                        <div class='timer-unit'>
                                            <div class='timer-value' id='hours$event_id'>00</div>
                                            <div class='timer-label'>Hours</div>
                                        </div>
                                        <div class='timer-separator'>:</div>
                                        <div class='timer-unit'>
                                            <div class='timer-value' id='minutes$event_id'>00</div>
                                            <div class='timer-label'>Minutes</div>
                                        </div>
                                        <div class='timer-separator'>:</div>
                                        <div class='timer-unit'>
                                            <div class='timer-value' id='seconds$event_id'>00</div>
                                            <div class='timer-label'>Seconds</div>
                                        </div>
                                    </div>

                                    <p class='event-description'>
                                        $event_description
                                    </p>
                                    
                                    <!-- Event Highlights -->
                                    <div class='event-highlights'>
                                        <div class='highlight-item'>
                                            <span class='highlight-value'>50K+</span> 
                                            <span class='highlight-label'>Expected Footfall</span>
                                        </div>
                                        <div class='highlight-divider'></div>
                                        <div class='highlight-item'>
                                            <span class='highlight-value'>50+</span> 
                                            <span class='highlight-label'>Premium Stalls</span>
                                        </div>
                                        <div class='highlight-divider'></div>
                                        <div class='highlight-item'>
                                            <span class='highlight-value'>Luxury</span> 
                                            <span class='highlight-label'>Event Category</span>
                                        </div>
                                    </div><!-- CTA Section -->
                                    <div class='cta-wrapper'>
                                        <a href='$event_form_link' target='_blank' class='book-stall-btn'>Book Now</a> 
                                        <span class='cta-subtext'>*Limited Stalls Available</span>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <script>
                            // Countdown Timer
                            function initCountdownTimer$event_id() {
                                const eventDate$event_id = new Date('$js_date').getTime();
                                
                                function updateTimer$event_id() {
                                    const now$event_id = new Date().getTime();
                                    const timeLeft$event_id = eventDate$event_id - now$event_id;
                                    
                                    const days$event_id = Math.floor(timeLeft$event_id / (1000 * 60 * 60 * 24));
                                    const hours$event_id = Math.floor((timeLeft$event_id % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                    const minutes$event_id = Math.floor((timeLeft$event_id % (1000 * 60 * 60)) / (1000 * 60));
                                    const seconds$event_id = Math.floor((timeLeft$event_id % (1000 * 60)) / 1000);
                                    
                                    const daysEl$event_id = document.getElementById('days$event_id');
                                    const hoursEl$event_id = document.getElementById('hours$event_id');
                                    const minutesEl$event_id = document.getElementById('minutes$event_id');
                                    const secondsEl$event_id = document.getElementById('seconds$event_id');
                                    
                                    if (daysEl$event_id) daysEl$event_id.textContent = String(days$event_id).padStart(2, '0');
                                    if (hoursEl$event_id) hoursEl$event_id.textContent = String(hours$event_id).padStart(2, '0');
                                    if (minutesEl$event_id) minutesEl$event_id.textContent = String(minutes$event_id).padStart(2, '0');
                                    if (secondsEl$event_id) secondsEl$event_id.textContent = String(seconds$event_id).padStart(2, '0');
                                    
                                    // Stop if event date has passed
                                    if (timeLeft$event_id < 0) {
                                        if (daysEl$event_id) daysEl$event_id.textContent = '00';
                                        if (hoursEl$event_id) hoursEl$event_id.textContent = '00';
                                        if (minutesEl$event_id) minutesEl$event_id.textContent = '00';
                                        if (secondsEl$event_id) secondsEl$event_id.textContent = '00';
                                        clearInterval(timerInterval$event_id);
                                    }
                                }
                                
                                // Update immediately on load
                                updateTimer$event_id();
                                
                                // Update every second
                                const timerInterval$event_id = setInterval(updateTimer$event_id, 1000);
                            }
                            
                            // Initialize timer when DOM is ready
                            if (document.readyState === 'loading') {
                                document.addEventListener('DOMContentLoaded', () => {
                                    initCountdownTimer$event_id();
                                });
                            } else {
                                initCountdownTimer$event_id();
                            }
                        </script>
                    ";
                }
                mysqli_stmt_close($stmt_nearest_events);
            }

            // 2) Include any other events within the next 7 days (dedupe)
            $stmt_within_7 = mysqli_prepare($con, "
                SELECT e.*, c.*, v.* FROM events e
                LEFT JOIN cities c ON e.event_city = c.city_id
                LEFT JOIN venues v ON e.event_venue = v.venue_id
                WHERE event_status = 'active'
                AND event_from <= DATE_ADD(NOW(), INTERVAL 7 DAY)
                ORDER BY event_from ASC, event_id DESC
            ");

            mysqli_stmt_execute($stmt_within_7);
            $result_within_7 = mysqli_stmt_get_result($stmt_within_7);

            while ($result_within_7 && ($within_7 = mysqli_fetch_assoc($result_within_7))) {
                $event_id = $within_7['event_id'];
                if (isset($seen_ids[$event_id])) {
                    continue; // Skip already included events
                }
                $seen_ids[$event_id] = true;
                // Get First 3 Images for this City
                $stmt_city_image = mysqli_prepare($con, "
                    SELECT * FROM images
                    WHERE image_for_content = 'cities'
                    AND image_for_content_id = ?
                    AND image_status = 'active'
                    ORDER BY image_id DESC
                    LIMIT 0, 3
                ");

                mysqli_stmt_bind_param($stmt_city_image, 'i', $within_7['event_city']);
                mysqli_stmt_execute($stmt_city_image);
                $result_city_image = mysqli_stmt_get_result($stmt_city_image);

                $city_images = [];

                while ($image_row = mysqli_fetch_assoc($result_city_image)) {
                    $city_images[] = $image_row['image_name'];
                }

                if (count($city_images) <= 0) {
                    // If No Images found, use thumbnail for all three images
                    $city_images = [
                        $within_7['event_thumbnail'],
                        $within_7['event_thumbnail'],
                        $within_7['event_thumbnail']
                    ];
                }
                mysqli_stmt_close($stmt_city_image);

                $city_name = htmlspecialchars($within_7['city_name']);
                $event_name = htmlspecialchars($within_7['event_name']);
                $venue_name = !empty($within_7['venue_name']) ? htmlspecialchars($within_7['venue_name']) : "To Be Announced";
                
                // Smart date formatting
                $from_timestamp = strtotime($within_7['event_from']);
                $to_timestamp = strtotime($within_7['event_to']);
                $from_month = date("m", $from_timestamp);
                $to_month = date("m", $to_timestamp);
                $from_year = date("Y", $from_timestamp);
                $to_year = date("Y", $to_timestamp);
                if (empty($within_7['event_from']) && empty($within_7['event_to'])) {
                    $event_date_display = "To Be Announced";
                } elseif ($from_month === $to_month && $from_year === $to_year) {
                    // Same month and year: "17 - 18 Jan, 2026"
                    $event_date_display = date("M d", $from_timestamp) . " - " . date("d, Y", $to_timestamp);
                } elseif ($from_year === $to_year) {
                    // Same year, different months: "30 Jan - 2 Feb, 2026"
                    $event_date_display = date("M d", $from_timestamp) . " - " . date("M d, Y", $to_timestamp);
                } else {
                    // Different years: "30 Dec, 2025 - 2 Jan, 2026"
                    $event_date_display = date("M d, Y", $from_timestamp) . " - " . date("M d, Y", $to_timestamp);
                }

                $event_form_link = htmlspecialchars($within_7['event_form_link']);
                $event_description = nl2br(htmlspecialchars($within_7['event_description']));

                $js_date = date("F d, Y", strtotime($within_7['event_from']));

                echo "
                    <article class='featured-slide'>
                        <div class='featured-event-container'>
                            <!-- Left Side: City Visual Experience -->
                            <div class='city-visual-wrapper'>
                                <div class='city-image-grid'>
                                    <!-- Main Large Image -->
                                    <div class='city-image-item'>
                                        <img src='" . get_site_option('dashboard_url') . "assets/uploads/images/events/" . $city_images[0] . "' alt='Mumbai skyline and culture' class='city-image-bg'>
                                    </div>
                                    <!-- Top Right Image -->
                                    <div class='city-image-item'>
                                        <img src='" . get_site_option('dashboard_url') . "assets/uploads/images/events/" . $city_images[1] . "' alt='Lifestyle exhibition crowd' class='city-image-bg'>
                                    </div>
                                    <!-- Bottom Right Image -->
                                    <div class='city-image-item'>
                                        <img src='" . get_site_option('dashboard_url') . "assets/uploads/images/events/" . $city_images[2] . "' alt='Premium venue interior' class='city-image-bg'>
                                    </div>
                                </div>
                                
                                <!-- City Title Overlay -->
                                <div class='city-title-overlay'>
                                    <h2>$city_name</h2>
                                    <div class='city-descriptor'>
                                        Luxury • Energy • Scale
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side: Event Details -->
                            <div class='event-details-wrapper'>
                                <h1 class='event-title'>$event_name</h1>
                                <div class='event-location'>
                                    <i class='fas fa-map-marker-alt'></i>$venue_name
                                </div>
                                <div class='event-date'>
                                    <i class='fas fa-calendar-alt'></i>$event_date_display
                                </div>
                                <div class='countdown-timer' id='countdownTimer'>
                                    <div class='timer-unit'>
                                        <div class='timer-value' id='days" . $event_id . "'>00</div>
                                        <div class='timer-label'>Days</div>
                                    </div>
                                    <div class='timer-separator'>:</div>
                                    <div class='timer-unit'>
                                        <div class='timer-value' id='hours" . $event_id . "'>00</div>
                                        <div class='timer-label'>Hours</div>
                                    </div>
                                    <div class='timer-separator'>:</div>
                                    <div class='timer-unit'>
                                        <div class='timer-value' id='minutes" . $event_id . "'>00</div>
                                        <div class='timer-label'>Minutes</div>
                                    </div>
                                    <div class='timer-separator'>:</div>
                                    <div class='timer-unit'>
                                        <div class='timer-value' id='seconds" . $event_id . "'>00</div>
                                        <div class='timer-label'>Seconds</div>
                                    </div>
                                </div>

                                <p class='event-description'>
                                    $event_description
                                </p>
                                
                                <!-- Event Highlights -->
                                <div class='event-highlights'>
                                    <div class='highlight-item'>
                                        <span class='highlight-value'>50K+</span> 
                                        <span class='highlight-label'>Expected Footfall</span>
                                    </div>
                                    <div class='highlight-divider'></div>
                                    <div class='highlight-item'>
                                        <span class='highlight-value'>50+</span> 
                                        <span class='highlight-label'>Premium Stalls</span>
                                    </div>
                                    <div class='highlight-divider'></div>
                                    <div class='highlight-item'>
                                        <span class='highlight-value'>Luxury</span> 
                                        <span class='highlight-label'>Event Category</span>
                                    </div>
                                </div><!-- CTA Section -->
                                <div class='cta-wrapper'>
                                    <a href='$event_form_link' target='_blank' class='book-stall-btn'>Book Now</a> 
                                    <span class='cta-subtext'>*Limited Stalls Available</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <script>
                        // Countdown Timer
                        function initCountdownTimer$event_id() {
                            const eventDate$event_id = new Date('$js_date').getTime();
                            
                            function updateTimer$event_id() {
                                const now$event_id = new Date().getTime();
                                const timeLeft$event_id = eventDate$event_id - now$event_id;
                                
                                const days$event_id = Math.floor(timeLeft$event_id / (1000 * 60 * 60 * 24));
                                const hours$event_id = Math.floor((timeLeft$event_id % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                const minutes$event_id = Math.floor((timeLeft$event_id % (1000 * 60 * 60)) / (1000 * 60));
                                const seconds$event_id = Math.floor((timeLeft$event_id % (1000 * 60)) / 1000);
                                
                                const daysEl$event_id = document.getElementById('days$event_id');
                                const hoursEl$event_id = document.getElementById('hours$event_id');
                                const minutesEl$event_id = document.getElementById('minutes$event_id');
                                const secondsEl$event_id = document.getElementById('seconds$event_id');
                                
                                if (daysEl$event_id) daysEl$event_id.textContent = String(days$event_id).padStart(2, '0');
                                if (hoursEl$event_id) hoursEl$event_id.textContent = String(hours$event_id).padStart(2, '0');
                                if (minutesEl$event_id) minutesEl$event_id.textContent = String(minutes$event_id).padStart(2, '0');
                                if (secondsEl$event_id) secondsEl$event_id.textContent = String(seconds$event_id).padStart(2, '0');
                                
                                // Stop if event date has passed
                                if (timeLeft$event_id < 0) {
                                    if (daysEl$event_id) daysEl$event_id.textContent = '00';
                                    if (hoursEl$event_id) hoursEl$event_id.textContent = '00';
                                    if (minutesEl$event_id) minutesEl$event_id.textContent = '00';
                                    if (secondsEl$event_id) secondsEl$event_id.textContent = '00';
                                    clearInterval(timerInterval$event_id);
                                }
                            }
                            
                            // Update immediately on load
                            updateTimer$event_id();
                            
                            // Update every second
                            const timerInterval$event_id = setInterval(updateTimer$event_id, 1000);
                        }
                        
                        // Initialize timer when DOM is ready
                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', () => {
                                initCountdownTimer$event_id();
                            });
                        } else {
                            initCountdownTimer$event_id();
                        }
                    </script>
                ";
            }
        }
    }

    function load_gallery($display_type = null, $order_by = 'gallery_id', $sorting = 'DESC', $limit = null) {
        global $con;
        
        if (empty($display_type) || $display_type == NULL) {
            echo '<p class="text-muted">Display type is required in Function Call.</p>';
            return;
        }

        if ($display_type === 'homepage-bottom') {
            $query = "SELECT * FROM gallery WHERE is_featured = 1 AND gallery_status = 'published' ORDER BY $order_by $sorting";

            if ($limit !== null && is_numeric($limit)) {
                $query .= " LIMIT ?";
            }

            $stmt = mysqli_prepare($con, $query);

            if ($limit !== null && is_numeric($limit)) {
                mysqli_stmt_bind_param($stmt, 'i', $limit);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) <= 0) {
                echo '<p class="text-muted">No images found in the gallery.</p>';
                return;
            }

            while ($image = mysqli_fetch_assoc($result)) {
                $image_title = htmlspecialchars($image['gallery_title']);
                $image_src = htmlspecialchars($image['gallery_image']);
                echo "
                    <div class='memory-frame'>
                        <img src='" . get_site_option('dashboard_url') . "assets/uploads/images/gallery/$image_src' alt='$image_title' class='frame-image'>
                        <div class='frame-caption'>
                            $image_title
                        </div>
                    </div>
                ";
            }
        }

        if ($display_type === 'gallery-page') {
            $query = "
                SELECT * FROM gallery
                WHERE gallery_status = 'published'
                ORDER BY $order_by $sorting
            ";

            if ($limit !== null && is_numeric($limit)) {
                $query .= " LIMIT ?";
            }

            $stmt = mysqli_prepare($con, $query);
            if ($limit !== null && is_numeric($limit)) {
                mysqli_stmt_bind_param($stmt, 'i', $limit);
            }
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) <= 0) {
                echo '
                    <div class="gallery-empty">
                        <div class="gallery-empty-icon">
                            <i class="fas fa-image"></i>
                        </div>
                        <h3>No Gallery Images Yet</h3>
                        <p>Gallery images will be displayed here soon. Please check back later!</p>
                    </div>
                ';
            }

            $index = 0;

            while ($image = mysqli_fetch_assoc($result)) {
                $image_name = htmlspecialchars($image['gallery_image']);
                $image_title = htmlspecialchars($image['gallery_title']);
                $safe_title = !empty($image_title) ? $image_title : 'Gallery Image';

                echo "
                    <div class='gallery-item' data-gallery-index='$index' data-image-name='$image_name' data-image-title='$safe_title'>
                        <img src='" . get_site_option('dashboard_url') . "assets/uploads/images/gallery/$image_name' alt='$safe_title' class='gallery-img'>
                        <div class='gallery-overlay'>
                            <button type='button' class='gallery-zoom' aria-label='Open image in gallery viewer'>
                                <i class='fas fa-search-plus'></i>
                            </button>
                        </div>
                    </div>
                ";

                $index++;
            }
        }

        // if ($display_type === 'gallery-management') {
        //     $query = "SELECT g.*, om.* FROM gallery g
        //         LEFT JOIN office_members om ON g.gallery_uploaded_by = om.office_member_unique_id
        //         WHERE g.gallery_status != 'deleted' ORDER BY $order_by $sorting";

        //     $stmt = mysqli_prepare($con, $query);
        //     mysqli_stmt_execute($stmt);
        //     $result = mysqli_stmt_get_result($stmt);

        //     if (mysqli_num_rows($result) <= 0) {
        //         echo "
        //             <tr class='table-empty'>
        //                 <td class='text-center text-muted'>—</td>
        //                 <td class='text-muted'>No images found.</td>
        //                 <td class='text-muted'>&nbsp;</td>
        //                 <td class='text-muted'>&nbsp;</td>
        //                 <td class='text-muted'>&nbsp;</td>
        //             </tr>
        //         ";
        //         return;
        //     }

        //     $i = 0;
        //     while ($image = mysqli_fetch_assoc($result)) {
        //         $i++;
        //         $image_id = $image['gallery_id'];
        //         $image_title = htmlspecialchars($image['gallery_title']);
        //         $image_src = htmlspecialchars($image['gallery_image']);
        //         $image_status = htmlspecialchars($image['gallery_status']);
        //             if ($image_status === 'published') {
        //                 $image_status_badge = "
        //                     <button class='bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm' onclick=\"confirmStatusChange('gallery', 'gallery_id', $image_id, 'gallery_status', 'draft', 'gallery_updated_on')\"> " . ucwords(strtolower($image_status)) . "</button>
        //                 ";
        //             } elseif ($image_status === 'draft') {
        //                 $image_status_badge = "
        //                     <button class='bg-warning-focus text-warning-main px-24 py-4 rounded-pill fw-medium text-sm' onclick=\"confirmStatusChange('gallery', 'gallery_id', $image_id, 'gallery_status', 'published', 'gallery_updated_on')\"> " . ucwords(strtolower($image_status)) . "</button>
        //                 ";
        //             }

        //         $image_uploaded_by_id = htmlspecialchars($image['gallery_uploaded_by']);
        //         $image_uploaded_by_salutation = ucwords(strtolower($image['office_member_salutation']));
        //         $image_uploaded_by_fullname = ucwords(strtolower(htmlspecialchars($image['office_member_fullname'])));
        //         $image_uploaded_by_name = $image_uploaded_by_salutation . ' ' . $image_uploaded_by_fullname;

        //         $image_uploaded_on = date('d M, Y h:i A', strtotime($image['gallery_uploaded_on']));
        //         $image_updated_on = $image['gallery_updated_on'];
        //             if (!empty($image_updated_on) || $image_updated_on !== NULL) {
        //                 $image_updated_on = date('d M, Y h:i A', strtotime($image['gallery_updated_on']));
        //             } else {
        //                 $image_updated_on = 'N/A';
        //             }

        //         echo "
        //             <tr>
        //                 <td class='text-center'>
        //                     $i
        //                 </td>
        //                 <td class='text-center'>
        //                     <img src='" . get_site_option('dashboard_url') . "assets/uploads/images/gallery/$image_src' alt='$image_title' width='50%' style='object-fit: cover; border-radius: 8px;' />
        //                 </td>
        //                 <td>$image_title</td>
        //                 <td>$image_status_badge</td>
        //                 <td>
        //                     <a href='javascript:void(0)' class='w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center' data-bs-toggle='modal' data-bs-target='#viewImageModal$image_id'>
        //                         <iconify-icon icon='iconamoon:eye-light'></iconify-icon>
        //                     </a>

        //                     <!--- View Image Modal -->
        //                     <div class='modal fade' id='viewImageModal$image_id' tabindex='-1' aria-labelledby='viewImageModalLabel$image_id' aria-hidden='true'>
        //                         <div class='modal-dialog modal-lg modal-dialog-centered'>
        //                             <div class='modal-content'>
        //                                 <div class='modal-header'>
        //                                     <h5 class='modal-title' id='viewImageModalLabel$image_id'>Image Details</h5>
        //                                     <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
        //                                 </div>
        //                                 <div class='modal-body'>
        //                                     <div class='row'>
        //                                         <div class='col-md-2'>
        //                                             <div class='mb-3'>
        //                                                 <label for='imageID' class='form-label'>ID:</label>
        //                                                 <input type='text' readonly value='$image_id' class='form-control' />
        //                                             </div>
        //                                         </div>
        //                                         <div class='col-md-6'>
        //                                             <div class='mb-3'>
        //                                                 <label for='imageAddedBy' class='form-label'>Added By:</label>
        //                                                 <input type='text' readonly value='$image_uploaded_by_name ($image_uploaded_by_id)' class='form-control' />
        //                                             </div>
        //                                         </div>
        //                                         <div class='col-md-4'>
        //                                             <div class='mb-3'>
        //                                                 <label for='imageUploadedOn' class='form-label'>Uploaded On:</label>
        //                                                 <input type='text' readonly value='$image_uploaded_on' class='form-control' />
        //                                             </div>
        //                                         </div>
        //                                         <div class='col-md-12'>
        //                                             <div class='mb-3'>
        //                                                 <label for='imageTitle' class='form-label'>Title:</label>
        //                                                 <input type='text' readonly value='$image_title' class='form-control' />
        //                                             </div>
        //                                         </div>
        //                                         <div class='col-md-12'>
        //                                             <div class='mb-3'>
        //                                                 <label for='imageFile' class='form-label'>Image:</label>
        //                                                 <img src='" . get_site_option('dashboard_url') . "assets/uploads/images/gallery/$image_src' alt='$image_title' width='100%' style='border: 1px solid #ccc; border-radius: 8px;' />
        //                                             </div>
        //                                         </div>
        //                                         <div class='col-md-3'>
        //                                             <div class='mb-3'>
        //                                                 <label for='imageStatus' class='form-label'>Status:</label>
        //                                                 <input type='text' readonly value='" . ucwords($image_status) . "' class='form-control' />
        //                                             </div>
        //                                         </div>
        //                                         <div class='col-md-5'>
        //                                             <div class='mb-3'>
        //                                                 <label for='imageUpdatedOn' class='form-label'>Updated On:</label>
        //                                                 <input type='text' readonly value='$image_updated_on' class='form-control' />
        //                                             </div>
        //                                         </div>
        //                                     </div>
        //                                 </div>
        //                                 <div class='modal-footer d-flex justify-content-between'>
        //                                     <div>";
        //                                         if ($image_status === 'published') {
        //                                             echo "
        //                                                 <button type='button' class='btn btn-warning' onclick=\"confirmStatusChange('gallery', 'gallery_id', $image_id, 'image_status', 'draft', 'image_updated_on')\">
        //                                                     <i class='ri-error-warning-line'></i> Mark as Draft
        //                                                 </button>
        //                                             ";
        //                                         } elseif ($image_status === 'draft') {
        //                                             echo "
        //                                                 <button type='button' class='btn btn-success' onclick=\"confirmStatusChange('gallery', 'gallery_id', $image_id, 'image_status', 'published', 'image_updated_on')\">
        //                                                     <i class='ri-hand-coin-line'></i> Mark as Published
        //                                                 </button>
        //                                             ";
        //                                         }
        //                                         echo "
        //                                         <button type='button' class='btn btn-danger' onclick=\"confirmStatusChange('gallery', 'gallery_id', $image_id, 'image_status', 'deleted', 'image_updated_on')\">
        //                                             <i class='ri-delete-bin-line'></i> Delete
        //                                         </button>
        //                                     </div>
        //                                     <button type='button' class='btn btn-outline-secondary' data-bs-dismiss='modal'>Close</button>
        //                                 </div>
        //                             </div>
        //                         </div>
        //                     </div>
                            

        //                     <a href='" . get_site_option('dashboard_url') . "?page=edit-image&image_id=$image_id' class='w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center'>
        //                         <iconify-icon icon='lucide:edit'></iconify-icon>
        //                     </a>

        //                     <a href='javascript:void(0);' class='w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center' onclick=\"confirmStatusChange('gallery', 'gallery_id', $image_id, 'image_status', 'deleted', 'image_updated_on')\">
        //                         <iconify-icon icon='mingcute:delete-2-line'></iconify-icon>
        //                     </a>
        //                 </td>
        //             </tr>
        //         ";
        //     }
        // }
    }

    function load_single_exhibition_carousel($event_city) {
        global $con;

        if (empty($event_city)) {
            return null;
        }

        $query = "
            SELECT i.*, e.* FROM images i
            LEFT JOIN events e ON i.image_for_content_id = e.event_city
            WHERE i.image_for_content = 'cities'
            AND i.image_for_content_id = ?
            AND i.image_status = 'active'
            AND e.event_status = 'active'
            ORDER BY i.image_id DESC
            LIMIT 0, 3
        ";

        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $event_city);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) <= 0) {
            return null;
        }

        while ($image = mysqli_fetch_assoc($result)) {
            $index = 0;
                if ($index === 0) {
                    $active = 'active';
                } else {
                    $active = '';
                }
            echo "
                <div class='carousel-slide $active'>
                    <img src='" . get_site_option('dashboard_url') . "assets/uploads/images/events/" . htmlspecialchars($image['image_name']) . "' 
                            alt='" . htmlspecialchars($image['event_name']) . " - Image " . ($index + 1) . "' 
                            class='carousel-image'
                            draggable='false'>
                </div>
            ";
            $index++;
        }
    }

    function get_event_details($event_id = null, $event_slug = null) {
        global $con;
        
        // Validate that at least one parameter is provided
        if (empty($event_id) && empty($event_slug)) {
            return null;
        }
        
        // Build the WHERE clause based on provided parameters
        $where_conditions = [];
        $param_types = '';
        $param_values = [];
        
        if (!empty($event_id)) {
            $where_conditions[] = "e.event_id = ?";
            $param_types .= 'i';
            $param_values[] = $event_id;
        }
        
        if (!empty($event_slug)) {
            $where_conditions[] = "e.event_slug = ?";
            $param_types .= 's';
            $param_values[] = $event_slug;
        }
        
        // Join conditions with AND or OR based on whether both parameters are provided
        $where_clause = implode(' AND ', $where_conditions);
        
        // Prepare the query with JOIN to get city and venue details
        $query = "
            SELECT e.*, c.*, v.* 
            FROM events e 
            LEFT JOIN cities c ON e.event_city = c.city_id
            LEFT JOIN venues v ON e.event_venue = v.venue_id
            WHERE $where_clause
            LIMIT 1
        ";
        
        $stmt = mysqli_prepare($con, $query);
        
        // Bind parameters dynamically
        if (!empty($param_types)) {
            mysqli_stmt_bind_param($stmt, $param_types, ...$param_values);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $event = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if (!$event) {
            return null;
        }
        
        // Fetch all images for this event
        $images_query = "
            SELECT * FROM images 
            WHERE image_for_content = 'events' 
            AND image_for_content_id = ? 
            AND image_status = 'active' 
            ORDER BY image_id DESC
        ";
        
        $images_stmt = mysqli_prepare($con, $images_query);
        mysqli_stmt_bind_param($images_stmt, 'i', $event['event_id']);
        mysqli_stmt_execute($images_stmt);
        $images_result = mysqli_stmt_get_result($images_stmt);
        
        $event_images = [];
        while ($image_row = mysqli_fetch_assoc($images_result)) {
            $event_images[] = $image_row;
        }
        mysqli_stmt_close($images_stmt);
        
        // Add images array to event data
        $event['event_images'] = $event_images;
        
        // Also create a simple array of image names for backward compatibility
        $event['image_names'] = array_column($event_images, 'image_name');
        
        return $event;
    }

    function load_single_exhibition_gallery($event_id) {
        global $con;

        if (empty($event_id)) {
            return null;
        }

        $query = "
            SELECT * FROM images
            WHERE image_for_content = 'events'
            AND image_for_content_id = ?
            AND image_status = 'active'
            ORDER BY image_id DESC
            LIMIT 0, 8
        ";

        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $event_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) <= 0) {
            return null;
        }

        $index = 0;

        while ($image = mysqli_fetch_assoc($result)) {
            $image_name = htmlspecialchars($image['image_name']);
            $image_title = htmlspecialchars($image['image_title']);
            $safe_title = !empty($image_title) ? $image_title : 'Gallery Image';

            echo "
                <div class='gallery-item' data-gallery-index='$index' data-image-name='$image_name' data-image-title='$safe_title'>
                    <img src='" . get_site_option('dashboard_url') . "assets/uploads/images/events/$image_name' alt='$safe_title' class='gallery-img'>
                    <div class='gallery-overlay'>
                        <button type='button' class='gallery-zoom' aria-label='Open image in gallery viewer'>
                            <i class='fas fa-search-plus'></i>
                        </button>
                    </div>
                </div>
            ";

            $index++;
        }
    }

    function load_single_exhibition_plans($event_id) {
        global $con;

        if (empty($event_id)) {
            return null;
        }

        $query = "
            SELECT p.*, e.* FROM packages p
            LEFT JOIN events e ON p.package_for_event = e.event_id
            WHERE package_for_event = ?
            AND package_status = 'active'
            ORDER BY package_id ASC
        ";

        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $event_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) <= 0) {
            echo '<p class="text-muted">No exhibition plans available at the moment.</p>';
            return;
        }

        while ($plan = mysqli_fetch_assoc($result)) {
            $plan_name = htmlspecialchars($plan['package_title']);
                if ($plan_name === "Laminated Table Stall") {
                    $plan_name = "Laminated Table<br>Stall";
                } elseif ($plan_name === "6 X 6 Size Fabricated Stall") {
                    $plan_name = "6 X 6 Size Fabricated<br>Stall";
                } elseif ($plan_name === "L-Shape 6X6 Size Fabricated Stall") {
                    $plan_name = "L-Shape 6X6 Size<br>Fabricated Stall";
                } elseif ($plan_name === "12 X 6 Size Fabricated Stall") {
                    $plan_name = "12 X 6 Size Fabricated<br>Stall";
                }
            $plan_subtitle = nl2br(htmlspecialchars($plan['package_subtitle']));
            $plan_price = number_format($plan['package_price'], 0);
            
            // Handle original price and discount calculation
            $original_price_raw = !empty($plan['package_original_price']) ? $plan['package_original_price'] : null;
            $plan_original_price = '';
            
            if (!empty($original_price_raw) && $original_price_raw > $plan['package_price']) {
                $formatted_original_price = "₹" . number_format($original_price_raw, 0);
                $discount_amt = $original_price_raw - $plan['package_price'];
                $discount_percent = round(($discount_amt / $original_price_raw) * 100);
                $plan_original_price = "<p style='font-size: 16px; margin-bottom: 30px;'><span style='font-size: 20px; text-decoration: line-through; margin-bottom: 30px;'>$formatted_original_price</span> (Save $discount_percent%)</p>";
            } else {
                $plan_original_price = "<span style='font-size: 20px; margin-bottom: 30px;'>Original Price</span>";
            }

            $package_features = $plan['package_details'];
            if ($plan['is_featured'] === 1) {
                $featured = 'featured';
                $badge = '<div class="pricing-badge">Most Popular</div>';
            } else {
                $featured = '';
                $badge = '';
            }
            $event_form_link = htmlspecialchars($plan['event_form_link']);
            echo "
                <div class='pricing-card $featured'>
                    $badge
                    <div class='pricing-header'>
                        <h3 class='pricing-name'>$plan_name</h3>
                    </div>
                    <div class='pricing-amount'>
                        <span class='currency'>₹</span>
                        <span class='price'>$plan_price</span>
                        <span class='period'>/stall</span>
                    </div>
                    $plan_original_price
                    <ul class='pricing-features'>
                        $package_features
                    </ul>
                    <div class='pricing-cta' style='display: flex; flex-direction: column; justify-content: flex-start; align-items: center;'>
                        <p class='pricing-description text-center mb-3'>$plan_subtitle</p>
                        <a href='$event_form_link' target='_blank' class='u-btn-secondary btn-full-width'>Book Now</a>
                    </div>
                </div>
            ";
        }
    }

    function get_total_numbers($table_name, $where_conditions = null) {
        global $con;

        $query = "SELECT COUNT(*) AS total FROM $table_name";
        $stmt = null;
        $param_types = '';
        $param_values = [];

        // Handle WHERE conditions
        if (!empty($where_conditions)) {
            $where_clauses = [];

            // If single condition (backward compatibility)
            if (isset($where_conditions['column'])) {
                $where_conditions = [$where_conditions];
            }

            // Process each condition
            foreach ($where_conditions as $condition) {
                $column = isset($condition['column']) ? $condition['column'] : null;
                $operator = isset($condition['operator']) ? strtoupper($condition['operator']) : '=';
                $value = isset($condition['value']) ? $condition['value'] : null;

                if (empty($column)) {
                    continue;
                }

                // Handle different operators
                switch ($operator) {
                    case 'IN':
                    case 'NOT IN':
                        if (is_array($value) && !empty($value)) {
                            $placeholders = implode(',', array_fill(0, count($value), '?'));
                            $where_clauses[] = "$column $operator ($placeholders)";
                            
                            // Add values and types
                            foreach ($value as $v) {
                                $param_values[] = $v;
                                $param_types .= is_numeric($v) ? 'i' : 's';
                            }
                        }
                        break;

                    case 'BETWEEN':
                        if (is_array($value) && count($value) === 2) {
                            $where_clauses[] = "$column BETWEEN ? AND ?";
                            $param_values[] = $value[0];
                            $param_values[] = $value[1];
                            $param_types .= is_numeric($value[0]) ? 'i' : 's';
                            $param_types .= is_numeric($value[1]) ? 'i' : 's';
                        }
                        break;

                    case 'IS NULL':
                    case 'IS NOT NULL':
                        $where_clauses[] = "$column $operator";
                        break;

                    case '=':
                    case '!=':
                    case '<>':
                    case '<':
                    case '>':
                    case '<=':
                    case '>=':
                    case 'LIKE':
                        $where_clauses[] = "$column $operator ?";
                        $param_values[] = $value;
                        $param_types .= is_numeric($value) && in_array($operator, ['=', '!=', '<>', '<', '>', '<=', '>=']) ? 'i' : 's';
                        break;

                    default:
                        // Default to = operator
                        $where_clauses[] = "$column = ?";
                        $param_values[] = $value;
                        $param_types .= is_numeric($value) ? 'i' : 's';
                        break;
                }
            }

            // Append WHERE clause if conditions exist
            if (!empty($where_clauses)) {
                $query .= " WHERE " . implode(" AND ", $where_clauses);
            }
        }

        // Prepare and execute statement
        $stmt = mysqli_prepare($con, $query);

        if (!empty($param_values)) {
            mysqli_stmt_bind_param($stmt, $param_types, ...$param_values);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $row['total'] ?? 0;
    }

    function get_office_details($office_detail_key) {
        global $con;
        
        $stmt = mysqli_prepare($con, "SELECT office_detail_value FROM office_details WHERE office_detail_key = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $office_detail_key);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if (!$row) {
            return null;
        }
        
        return $row['office_detail_value'];
    }

    function get_office_address() {
        $full_office_address = get_office_details('office_complete_address');
        
        if (empty($full_office_address)) {
            return '';
        }
        
        $office_address_parts = explode(',', $full_office_address);
        $office_address_parts = array_map('trim', $office_address_parts);
        
        $formatted_lines = [];
        
        // Add first part on its own line
        $formatted_lines[] = $office_address_parts[0];
        
        // Process remaining parts in pairs (2-2)
        for ($i = 1; $i < count($office_address_parts); $i += 2) {
            $line = $office_address_parts[$i];
            
            // Add the next part if it exists
            if (isset($office_address_parts[$i + 1])) {
                $line .= ', ' . $office_address_parts[$i + 1];
            }
            
            $formatted_lines[] = $line;
        }
        
        return implode(',<br>', $formatted_lines);
    }

    function get_office_hours() {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $office_hours = [];
        
        // Fetch hours for each day
        foreach ($days as $day) {
            $day_lower = strtolower($day);
            $open_hours = get_office_details('office_' . $day_lower . '_open_hours');
            $close_hours = get_office_details('office_' . $day_lower . '_close_hours');
            
            if (!empty($open_hours) && !empty($close_hours)) {
                $office_hours[$day] = [
                    'open' => $open_hours,
                    'close' => $close_hours,
                    'status' => 'open'
                ];
            } else {
                $office_hours[$day] = [
                    'open' => null,
                    'close' => null,
                    'status' => 'closed'
                ];
            }
        }
        
        $formatted_hours = [];
        $i = 0;
        
        while ($i < count($days)) {
            $current_day = $days[$i];
            $current_hours = $office_hours[$current_day];
            
            $start_day = $current_day;
            $end_day = $current_day;
            $j = $i + 1;
            
            // Group consecutive days with same status and hours
            while ($j < count($days)) {
                $next_day = $days[$j];
                $next_hours = $office_hours[$next_day];
                
                // Check if next day has same status and hours
                $same_status = $next_hours['status'] === $current_hours['status'];
                $same_hours = ($next_hours['open'] === $current_hours['open'] && 
                              $next_hours['close'] === $current_hours['close']);
                
                if ($same_status && $same_hours) {
                    $end_day = $next_day;
                    $j++;
                } else {
                    break;
                }
            }
            
            // Format the day range
            if ($start_day === $end_day) {
                $day_range = $start_day;
            } else {
                $day_range = $start_day . ' - ' . $end_day;
            }
            
            // Format the hours
            if ($current_hours['status'] === 'closed') {
                $formatted_hours[] = $day_range . ': <span style="color: red;">Office Remains Closed</span>';
            } else {
                $formatted_hours[] = $day_range . ': ' . $current_hours['open'] . ' - ' . $current_hours['close'];
            }
            
            $i = $j;
        }
        
        if (empty($formatted_hours)) {
            return '<div class="info-value"><p>Office hours not available</p></div>';
        }
        
        return implode('<br>', $formatted_hours);
    }

    function get_maps_embed_code() {
        $map_iframe = get_office_details('map_iframe');
        $latitude = get_office_details('map_latitude');
        $longitude = get_office_details('map_longitude');

        if (empty($map_iframe)) {
            if (empty($latitude) || empty($longitude)) {
                return '<p>Map location not available</p>';
            }

            // Construct simple Google Maps embed URL with coordinates
            // Format: https://maps.google.com/maps?q={latitude},{longitude}&z=15&output=embed
            $embed_url = "https://maps.google.com/maps?q=" . urlencode($latitude) . "," . urlencode($longitude) . "&z=15&output=embed";
            
            $iframe = '<iframe class="map-embed" src="' . htmlspecialchars($embed_url) . '" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Society location map"></iframe>';
        } else {
            // Extract the src URL from stored iframe code
            preg_match('/src="([^"]+)"/', $map_iframe, $matches);
            
            if (!empty($matches[1])) {
                $embed_url = $matches[1];
                $iframe = '<iframe class="map-embed" src="' . htmlspecialchars($embed_url) . '" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Society location map"></iframe>';
            } else {
                // If can't extract URL, return the original iframe as-is
                $iframe = $map_iframe;
            }
        }
        
        return $iframe;
    }


    // Fetch single Member details
    function get_single_vendor_details($vendor_id = null, $vendor_unique_id = null, $vendor_slug = null, $vendor_email = null) {
        global $con;

        // Validate that at least one parameter is provided
        if (empty($vendor_id) && empty($vendor_unique_id) && empty($vendor_slug) && empty($vendor_email)) {
            return null;
        }

        // Build the WHERE clause based on provided parameters
        $where_conditions = [];
        $param_types = '';
        $param_values = [];
        if (!empty($vendor_id)) {
            $where_conditions[] = "vendor_id = ?";
            $param_types .= 'i';
            $param_values[] = $vendor_id;
        }
        if (!empty($vendor_unique_id)) {
            $where_conditions[] = "vendor_unique_id = ?";
            $param_types .= 's';
            $param_values[] = $vendor_unique_id;
        }
        if (!empty($vendor_slug)) {
            $where_conditions[] = "vendor_slug = ?";
            $param_types .= 's';
            $param_values[] = $vendor_slug;
        }
        if (!empty($vendor_email)) {
            $where_conditions[] = "vendor_email = ?";
            $param_types .= 's';
            $param_values[] = $vendor_email;
        }

        // Join conditions with AND
        $where_clause = implode(' AND ', $where_conditions);

        $stmt = mysqli_prepare($con, "SELECT * FROM vendors WHERE $where_clause AND vendor_status = 'active' LIMIT 1");
        mysqli_stmt_bind_param($stmt, $param_types, ...$param_values);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $vendor = mysqli_fetch_assoc($result);
        if (!$vendor) {
            return null;
        }

        return $vendor;
    }

    // =============================================
    // CITIES SELECT BOX FOR FILTERING
    // =============================================
    function load_cities_select_options() {
    }



    // FETCHING COMMITTEE MEMBERS FUNCTIONS
    function fetch_committee_members($display_type = null, $limit = null) {
        global $con;
        
        if (empty($display_type) || $display_type == NULL) {
            echo '<p class="text-muted">Display type is required in Function Call.</p>';
            return;
        }

        if ($display_type === 'homepage') {
            $query = " SELECT * FROM managing_committee 
                WHERE committee_member_status = 'active' 
                ORDER BY committee_member_id DESC
            ";

            if ($limit !== null && is_numeric($limit)) {
                $query .= " LIMIT ?";
            }

            $stmt = mysqli_prepare($con, $query);

            if ($limit !== NULL && is_numeric($limit)) {
                mysqli_stmt_bind_param($stmt, 'i', $limit);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 0) {
                echo '<p class="text-muted">No committee members found.</p>';
                return;
            }

            while ($committee_member = mysqli_fetch_assoc($result)) {
                $fullname = htmlspecialchars($committee_member['committee_member_fullname']);
                $salutation = htmlspecialchars($committee_member['committee_member_salutation']);
                $role = htmlspecialchars($committee_member['committee_member_role']);
                $image = htmlspecialchars($committee_member['committee_member_image']);
                
                // Handle image or initial
                if (empty($image) || $image == NULL) {
                    $image_content = strtoupper(substr($fullname, 0, 1));
                } else {
                    $image_content = '<img src="' . get_site_option('dashboard_url') . 'assets/uploads/images/committee_members/' . $image . '" alt="' . $fullname . '" class="team-member-photo-img">';
                }

                $fullname_with_salutation = $salutation . ' ' . $fullname;
                
                echo '
                    <div class="team-member">
                        <div class="team-member-photo">
                            ' . $image_content . '
                        </div>
                        <div class="team-member-name">' . $fullname_with_salutation . '</div>
                        <div class="team-member-position">' . $role . '</div>
                    </div>
                ';
            }
        }

        if ($display_type === 'detailed_grid') {
            $query = " SELECT * FROM managing_committee 
                WHERE committee_member_status = 'active' 
                ORDER BY committee_member_id DESC
            ";

            if ($limit !== null && is_numeric($limit)) {
                $query .= " LIMIT ?";
            }

            $stmt = mysqli_prepare($con, $query);

            if ($limit !== NULL && is_numeric($limit)) {
                mysqli_stmt_bind_param($stmt, 'i', $limit);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 0) {
                echo '<p class="text-muted">No committee members found.</p>';
                return;
            }

            while ($committee_member = mysqli_fetch_assoc($result)) {
                $fullname = htmlspecialchars($committee_member['committee_member_fullname']);
                $salutation = htmlspecialchars($committee_member['committee_member_salutation']);
                $role = htmlspecialchars($committee_member['committee_member_role']);
                $image = htmlspecialchars($committee_member['committee_member_image']);
                
                // Handle image or initial
                if (empty($image) || $image == NULL) {
                    $image_content = strtoupper(substr($fullname, 0, 1));
                } else {
                    $image_content = '<img src="' . get_site_option('site_url') . 'assets/images/committee_members/' . $image . '" alt="' . $fullname . '" class="team-member-photo-img">';
                }

                $flat = !empty($committee_member['committee_member_flat']) ? htmlspecialchars($committee_member['committee_member_flat']) : '';
                $phone = !empty($committee_member['committee_member_phone_number']) ? htmlspecialchars($committee_member['committee_member_phone_number']) : '';
                $email = !empty($committee_member['committee_member_email_address']) ? htmlspecialchars($committee_member['committee_member_email_address']) : '';
                $term = !empty($committee_member['committee_member_term']) ? htmlspecialchars(substr($committee_member['committee_member_term'], 0, 4) . " - " . substr($committee_member['committee_member_term'], 9, 2)) : '';
                
                // Extract wing from flat (e.g., "A" from "A-101")
                $wing = '';
                if (!empty($flat) && preg_match('/^([A-Z])-/', $flat, $matches)) {
                    $wing = $matches[1];
                }
                
                // Get initials for avatar
                $name_parts = explode(' ', $fullname);
                $initials = '';
                foreach ($name_parts as $part) {
                    if (!empty($part)) {
                        $initials .= strtoupper(substr($part, 0, 1));
                    }
                }
                $initials = substr($initials, 0, 2);
                
                $fullname_with_salutation = $salutation . ' ' . $fullname;
                
                echo '
                    <div class="member-card">
                        <div class="member-header">
                            <div class="member-avatar" aria-hidden="true">' . $initials . '</div>
                            <div class="member-info">
                                <div class="member-name">' . $fullname_with_salutation . '</div>';
                
                // Only show flat if it exists
                if (!empty($flat)) {
                    echo '<div class="member-flat">Flat ' . $flat . '</div>';
                }
                
                echo '
                            </div>
                        </div>
                        <div class="member-position">' . $role . '</div>
                        <div class="member-details">';
                
                // Only show phone if it exists
                if (!empty($phone)) {
                    echo '
                            <div class="member-detail-item">
                                <span class="member-detail-icon"><i class="fas fa-phone"></i></span>
                                <span>' . $phone . '</span>
                            </div>';
                }
                
                // Only show email if it exists
                if (!empty($email)) {
                    echo '
                            <div class="member-detail-item">
                                <span class="member-detail-icon"><i class="fas fa-envelope"></i></span>
                                <span>' . $email . '</span>
                            </div>';
                }
                
                // Only show wing if it exists
                if (!empty($wing)) {
                    echo '
                            <div class="member-detail-item">
                                <span class="member-detail-icon"><i class="fas fa-building"></i></span>
                                <span>Block ' . $wing . '</span>
                            </div>';
                }
                
                echo '
                        </div>
                        <div class="member-badges">';
                
                // Only show term badge if term exists
                if (!empty($term)) {
                    echo '<span class="member-badge badge-term">Term ' . $term . '</span>';
                }
                
                echo '
                            <span class="member-badge badge-active">Active</span>
                        </div>
                    </div>
                ';
            }
        }

        if ($display_type === 'aboutpage') {
            $query = " SELECT * FROM managing_committee 
                WHERE committee_member_status = 'active' 
                ORDER BY committee_member_id DESC
            ";

            if ($limit !== null && is_numeric($limit)) {
                $query .= " LIMIT ?";
            }

            $stmt = mysqli_prepare($con, $query);

            if ($limit !== NULL && is_numeric($limit)) {
                mysqli_stmt_bind_param($stmt, 'i', $limit);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 0) {
                echo '<p class="text-muted">No committee members found.</p>';
                return;
            }

            while ($committee_member = mysqli_fetch_assoc($result)) {
                $fullname = htmlspecialchars($committee_member['committee_member_fullname']);
                $salutation = htmlspecialchars($committee_member['committee_member_salutation']);
                $role = htmlspecialchars($committee_member['committee_member_role']);
                $image = htmlspecialchars($committee_member['committee_member_image']);
                
                // Handle image or initial
                if (empty($image) || $image == NULL) {
                    $image_content = strtoupper(substr($fullname, 0, 1));
                } else {
                    $image_content = '<img src="' . get_site_option('site_url') . 'assets/images/committee_members/' . $image . '" alt="' . $fullname . '" class="team-member-photo-img">';
                }

                $fullname_with_salutation = $salutation . ' ' . $fullname;

                $flat = !empty($committee_member['committee_member_flat']) ? htmlspecialchars($committee_member['committee_member_flat']) : '';
                $phone = !empty($committee_member['committee_member_phone_number']) ? htmlspecialchars($committee_member['committee_member_phone_number']) : '';
                $email = !empty($committee_member['committee_member_email_address']) ? htmlspecialchars($committee_member['committee_member_email_address']) : '';
                $term = !empty($committee_member['committee_member_term']) ? htmlspecialchars(substr($committee_member['committee_member_term'], 0, 4) . " - " . substr($committee_member['committee_member_term'], 9, 2)) : '';
                
                // Extract wing from flat (e.g., "A" from "A-101")
                $wing = '';
                if (!empty($flat) && preg_match('/^([A-Z])-/', $flat, $matches)) {
                    $wing = $matches[1];
                }
                
                echo '
                    <div class="committee-member">
                        <div class="member-avatar" aria-hidden="true">
                            ' . $image_content . '
                        </div>
                        <div class="member-info">
                            <div class="member-name" id="chairman-name">
                                ' . $fullname_with_salutation . '
                            </div>
                            <div class="member-role" id="chairman-role">
                                ' . $role . '
                            </div>';
                    if (!empty($flat)) {
                        echo '
                            <div class="member-contact">
                                Flat No. ' . $flat . ' |
                        ';
                    }
                    echo '
                                Term: ' . $term . '
                            </div>
                        </div>
                    </div>  
                ';
            }
        }


        while ($committee_member = mysqli_fetch_assoc($result)) {
            $fullname = htmlspecialchars($committee_member['committee_member_fullname']);
            $salutation = htmlspecialchars($committee_member['committee_member_salutation']);
            $role = htmlspecialchars($committee_member['committee_member_role']);
            $image = htmlspecialchars($committee_member['committee_member_image']);
            
            // Handle image or initial
            if (empty($image) || $image == NULL) {
                $image_content = strtoupper(substr($fullname, 0, 1));
            } else {
                $image_content = '<img src="' . get_site_option('site_url') . 'assets/images/committee_members/' . $image . '" alt="' . $fullname . '" class="team-member-photo-img">';
            }

            switch ($display_type) {
                case 'detailed_grid':
                    // For committee members page
                    $flat = !empty($committee_member['committee_member_flat']) ? htmlspecialchars($committee_member['committee_member_flat']) : '';
                    $phone = !empty($committee_member['committee_member_phone_number']) ? htmlspecialchars($committee_member['committee_member_phone_number']) : '';
                    $email = !empty($committee_member['committee_member_email_address']) ? htmlspecialchars($committee_member['committee_member_email_address']) : '';
                    $term = !empty($committee_member['committee_member_term']) ? htmlspecialchars(substr($committee_member['committee_member_term'], 0, 4) . " - " . substr($committee_member['committee_member_term'], 9, 2)) : '';
                    
                    // Extract wing from flat (e.g., "A" from "A-101")
                    $wing = '';
                    if (!empty($flat) && preg_match('/^([A-Z])-/', $flat, $matches)) {
                        $wing = $matches[1];
                    }
                    
                    // Get initials for avatar
                    $name_parts = explode(' ', $fullname);
                    $initials = '';
                    foreach ($name_parts as $part) {
                        if (!empty($part)) {
                            $initials .= strtoupper(substr($part, 0, 1));
                        }
                    }
                    $initials = substr($initials, 0, 2);
                    
                    $fullname_with_salutation = $salutation . ' ' . $fullname;
                    
                    echo '
                        <div class="member-card">
                            <div class="member-header">
                                <div class="member-avatar" aria-hidden="true">' . $initials . '</div>
                                <div class="member-info">
                                    <div class="member-name">' . $fullname_with_salutation . '</div>';
                    
                    // Only show flat if it exists
                    if (!empty($flat)) {
                        echo '<div class="member-flat">Flat ' . $flat . '</div>';
                    }
                    
                    echo '
                                </div>
                            </div>
                            <div class="member-position">' . $role . '</div>
                            <div class="member-details">';
                    
                    // Only show phone if it exists
                    if (!empty($phone)) {
                        echo '
                                <div class="member-detail-item">
                                    <span class="member-detail-icon"><i class="fas fa-phone"></i></span>
                                    <span>' . $phone . '</span>
                                </div>';
                    }
                    
                    // Only show email if it exists
                    if (!empty($email)) {
                        echo '
                                <div class="member-detail-item">
                                    <span class="member-detail-icon"><i class="fas fa-envelope"></i></span>
                                    <span>' . $email . '</span>
                                </div>';
                    }
                    
                    // Only show wing if it exists
                    if (!empty($wing)) {
                        echo '
                                <div class="member-detail-item">
                                    <span class="member-detail-icon"><i class="fas fa-building"></i></span>
                                    <span>Block ' . $wing . '</span>
                                </div>';
                    }
                    
                    echo '
                            </div>
                            <div class="member-badges">';
                    
                    // Only show term badge if term exists
                    if (!empty($term)) {
                        echo '<span class="member-badge badge-term">Term ' . $term . '</span>';
                    }
                    
                    echo '
                                <span class="member-badge badge-active">Active</span>
                            </div>
                        </div>
                    ';
                    break;
                
                default:
                    echo '<p class="text-muted">Invalid display type specified.</p>';
                    break;
            }
        }
    }

    // Get unique wings for filter dropdown
    function get_unique_wings($status = 'active') {
        global $con;
        
        $query = "SELECT DISTINCT committee_member_flat 
                  FROM managing_committee 
                  WHERE committee_member_status = ? 
                  AND committee_member_flat IS NOT NULL 
                  AND committee_member_flat != ''
                  ORDER BY committee_member_flat ASC";
        
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 's', $status);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $wings = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $flat = $row['committee_member_flat'];
            // Extract wing letter (e.g., "A" from "A-101")
            if (preg_match('/^([A-Z])-/', $flat, $matches)) {
                $wing = $matches[1];
                if (!in_array($wing, $wings)) {
                    $wings[] = $wing;
                }
            }
        }
        
        sort($wings);
        return $wings;
    }



    // FETCHING NOTICES/ANNOUNCEMENTS/CIRCULARS/ETC. FUNCTIONS
    function fetch_notice($display_type = null, $status = 'published', $order_by = 'notice_id', $sorting = 'DESC', $limit = null, $archive_only = false) {
        global $con;
        
        if (empty($display_type) || $display_type == NULL) {
            echo '<p class="text-muted">Display type is required in Function Call.</p>';
        }

        if ($display_type === 'homepage') {
            $query = "SELECT n.*, nc.notice_category_name AS notice_category_name FROM notices n
                INNER JOIN notice_categories nc ON n.notice_category = nc.notice_category_id
                WHERE n.notice_status = ? AND nc.notice_category_status = 'active'";

            if ($archive_only === true) {
                $query .= " AND n.notice_posted_on < DATE_SUB(NOW(), INTERVAL 30 DAY)";
            } else {
                $query .= " AND n.notice_posted_on >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            }

            $query .= " ORDER BY $order_by $sorting";
            
            if ($limit !== null && is_numeric($limit)) {
                $query .= " LIMIT ?";
            }

            $stmt = mysqli_prepare($con, $query);

            if ($limit !== null && is_numeric($limit)) {
                mysqli_stmt_bind_param($stmt, 'si', $status, $limit);
            } else {
                mysqli_stmt_bind_param($stmt, 's', $status);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 0) {
                echo '<p class="text-muted">No notices found.</p>';
            }

            while ($notice = mysqli_fetch_assoc($result)) {
                $notice_id = $notice['notice_id'];
                $notice_number = htmlspecialchars($notice['notice_number']);
                $notice_title = htmlspecialchars($notice['notice_title']);
                $notice_category = htmlspecialchars($notice['notice_category_name']);
                $notice_badge = htmlspecialchars($notice['notice_badge']);
                $notice_date = date('d M, Y', strtotime($notice['notice_posted_on']));
                $notice_single_line = htmlspecialchars($notice['notice_single_line']);
                $notice_excerpt = nl2br(htmlspecialchars($notice['notice_excerpt']));
                $notice_file = htmlspecialchars($notice['notice_material']);
                    if (!empty($notice_file) && $notice_file != NULL) {
                        $download_url = get_site_option('dashboard_url') . 'assets/uploads/documents/notices/' . $notice_file;
                        $have_file = "<a href='$download_url' class='announcement-btn' download>Download</a>";
                    } else {
                        $download_url = 'javascript:void(0);';
                        $have_file = "";
                    }

                echo "
                    <li class='announcement-item'>
                        <div class='announcement-header'>
                            <div class='announcement-main'>
                                <div class='announcement-title'>
                                    $notice_title
                                </div>
                                <div class='announcement-meta'>
                                    Published on $notice_date • $notice_single_line
                                </div>
                            </div>
                            <span class='announcement-badge'>$notice_category</span>
                        </div>
                        <div class='announcement-actions'>
                            <a href='./notice-details/?notice_id=$notice_id' class='announcement-btn'>Read More</a>
                            $have_file
                        </div>
                    </li>
                ";
            }
        }

        if ($display_type === 'list_view') {
            $query = "SELECT n.*, nc.notice_category_name AS notice_category_name FROM notices n
                INNER JOIN notice_categories nc ON n.notice_category = nc.notice_category_id
                WHERE n.notice_status = ? AND nc.notice_category_status = 'active'";

            if ($archive_only === true) {
                $query .= " AND n.notice_posted_on < DATE_SUB(NOW(), INTERVAL 30 DAY)";
            } else {
                $query .= " AND n.notice_posted_on >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            }

            $query .= " ORDER BY $order_by $sorting";
            
            if ($limit !== null && is_numeric($limit)) {
                $query .= " LIMIT ?";
            }

            $stmt = mysqli_prepare($con, $query);

            if ($limit !== null && is_numeric($limit)) {
                mysqli_stmt_bind_param($stmt, 'si', $status, $limit);
            } else {
                mysqli_stmt_bind_param($stmt, 's', $status);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 0) {
                echo '<p class="text-muted">No notices found.</p>';
            }

            while ($notice = mysqli_fetch_assoc($result)) {
                $notice_id = $notice['notice_id'];
                $notice_number = htmlspecialchars($notice['notice_number']);
                $notice_title = htmlspecialchars($notice['notice_title']);
                $notice_category = htmlspecialchars($notice['notice_category_name']);
                $notice_badge = htmlspecialchars($notice['notice_badge']);
                $notice_date = date('d M, Y', strtotime($notice['notice_posted_on']));
                $notice_single_line = htmlspecialchars($notice['notice_single_line']);
                $notice_excerpt = nl2br(htmlspecialchars($notice['notice_excerpt']));
                $notice_file = htmlspecialchars($notice['notice_material']);
                    if (!empty($notice_file) && $notice_file != NULL) {
                        $download_url = get_site_option('dashboard_url') . 'assets/uploads/documents/notices/' . $notice_file;
                        $have_file = "<a href='$download_url' class='notice-btn' role='button' download><i class='fas fa-download'></i> Download PDF</a>";
                    } else {
                        $download_url = 'javascript:void(0);';
                        $have_file = "";
                    }
                $download_link_html = !empty($download_url) ? "<a href='$download_url' class='announcement-btn' download>Download</a>" : '';

                echo "
                    <article class='notice-card' data-category='" . strtolower($notice_category) . "' data-keywords='$notice_title $notice_single_line $notice_excerpt $notice_number $notice_date $notice_category'>
                        <div class='notice-header'>
                            <div class='notice-title-group'>
                                <a href='" . get_site_option('site_url') . "notice-details/?notice_id=$notice_id' class='notice-title-link' style='text-decoration:none;'><h4 class='notice-title' id='notice-1-title' style='cursor:pointer;'>$notice_title</h4></a>
                                <div class='notice-meta'>
                                    <span class='notice-meta-item'><i class='fas fa-calendar-alt'></i> Published: $notice_date</span>
                                    <span class='notice-meta-item'><i class='fas fa-user'></i> Issued by: Managing Committee</span>
                                    <span class='notice-meta-item'><i class='fas fa-file-alt'></i> Notice No: $notice_number</span>
                                </div>
                            </div>
                            <div class='notice-badges'>
                                <span class='notice-badge badge-meeting'>$notice_category</span>
                                ";
                                if ($notice_badge !== null && !empty($notice_badge)) {
                                    echo "
                                        <span class='notice-badge'>$notice_badge</span>
                                    ";
                                }
                                echo "
                            </div>
                        </div>
                        <div class='notice-content'>
                            $notice_excerpt
                        </div>
                        <div class='notice-actions'>
                            <a href='" . get_site_option('site_url') . "notice-details/?notice_id=$notice_id' class='notice-btn' role='button'><i class='fas fa-clipboard-list'></i> View Full Details</a>
                            $have_file
                        </div>
                    </article>
                ";
            }
        }

        if ($display_type === 'notices-management') {
            $query = "SELECT n.*, nc.notice_category_name AS notice_category_name, om.office_member_salutation, om.office_member_fullname FROM notices n
                INNER JOIN notice_categories nc ON n.notice_category = nc.notice_category_id
                LEFT JOIN office_members om ON n.notice_posted_by = om.office_member_unique_id
                WHERE n.notice_status != 'deleted' ORDER BY $order_by $sorting";

            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) <= 0) {
                echo "
                    <tr class='table-empty'>
                        <td class='text-center text-muted'>—</td>
                        <td class='text-muted'>No notices found.</td>
                        <td class='text-muted'>&nbsp;</td>
                        <td class='text-muted'>&nbsp;</td>
                        <td class='text-muted'>&nbsp;</td>
                        <td class='text-muted'>&nbsp;</td>
                    </tr>
                ";
            } else {
                $i = 0;
                while ($notice = mysqli_fetch_assoc($result)) {
                    $i++;
                    $notice_id = $notice['notice_id'];
                    $notice_number = htmlspecialchars($notice['notice_number']);
                        if (strlen($notice_number) > 15) {
                            $notice_number_display = '...' . substr($notice_number, -12);
                        } else {
                            $notice_number_display = $notice_number;
                        }
                    $notice_title = htmlspecialchars($notice['notice_title']);
                        if (strlen($notice_title) > 50) {
                            $notice_title_display = substr($notice_title, 0, 47) . '...';
                        } else {
                            $notice_title_display = $notice_title;
                        }
                    $notice_category = htmlspecialchars($notice['notice_category_name']);
                    $notice_badge = !empty(htmlspecialchars($notice['notice_badge'])) ? htmlspecialchars($notice['notice_badge']) : 'N/A';
                    $notice_status = htmlspecialchars($notice['notice_status']);
                        if ($notice_status === 'published') {
                            $notice_status_badge = "
                                <button class='bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm' onclick=\"confirmStatusChange('notices', 'notice_id', $notice_id, 'notice_status', 'draft', 'notice_updated_on')\"> " . ucwords(strtolower($notice_status)) . "</button>
                            ";
                        } elseif ($notice_status === 'draft') {
                            $notice_status_badge = "
                                <button class='bg-warning-focus text-warning-main px-24 py-4 rounded-pill fw-medium text-sm' onclick=\"confirmStatusChange('notices', 'notice_id', $notice_id, 'notice_status', 'published', 'notice_updated_on')\"> " . ucwords(strtolower($notice_status)) . "</button>
                            ";
                        }
                    
                    $notice_posted_by_id = htmlspecialchars($notice['notice_posted_by']);
                    $notice_posted_by_salutation = ucwords(strtolower($notice['office_member_salutation']));
                    $notice_posted_by_fullname = ucwords(strtolower($notice['office_member_fullname']));
                    $notice_posted_by_name = $notice_posted_by_salutation . ' ' . $notice_posted_by_fullname;

                    $notice_posted_on = date('d M, Y h:i A', strtotime($notice['notice_posted_on']));
                    $notice_single_line = htmlspecialchars($notice['notice_single_line']);
                    $notice_content = $notice['notice_content'];
                    $notice_excerpt = htmlspecialchars($notice['notice_excerpt']);
                    $notice_updated_on = $notice['notice_updated_on'];
                        if (!empty($notice_updated_on) || $notice_updated_on !== NULL) {
                            $notice_updated_on = date('d M, Y h:i A', strtotime($notice['notice_updated_on']));
                        } else {
                            $notice_updated_on = 'N/A';
                        }

                    $notice_material_title = htmlspecialchars($notice['notice_material_title']);
                    $notice_material = htmlspecialchars($notice['notice_material']);

                    echo "
                        <tr>
                            <td class='text-center'>
                                $i
                            </td>
                            <td>$notice_number_display</td>
                            <td>$notice_title_display</td>
                            <td>$notice_category</td>
                            <td>$notice_status_badge</td>
                            <td>
                                <a href='javascript:void(0)' class='w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center' data-bs-toggle='modal' data-bs-target='#viewNoticeModal$notice_id'>
                                    <iconify-icon icon='iconamoon:eye-light'></iconify-icon>
                                </a>

                                <!--- View Notice Modal -->
                                <div class='modal fade' id='viewNoticeModal$notice_id' tabindex='-1' aria-labelledby='viewNoticeModalLabel$notice_id' aria-hidden='true'>
                                    <div class='modal-dialog modal-lg modal-dialog-centered'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='viewNoticeModalLabel$notice_id'>Notice Details</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                            </div>
                                            <div class='modal-body'>
                                                <div class='row'>
                                                    <div class='col-md-2'>
                                                        <div class='mb-3'>
                                                            <label for='noticeID' class='form-label'>ID:</label>
                                                            <input type='text' readonly value='$notice_id' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-6'>
                                                        <div class='mb-3'>
                                                            <label for='noticePostedBy' class='form-label'>Posted By:</label>
                                                            <input type='text' readonly value='$notice_posted_by_name ($notice_posted_by_id)' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-4'>
                                                        <div class='mb-3'>
                                                            <label for='noticePostedOn' class='form-label'>Posted On:</label>
                                                            <input type='text' readonly value='$notice_posted_on' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-6'>
                                                        <div class='mb-3'>
                                                            <label for='noticeTitle' class='form-label'>Title:</label>
                                                            <textarea id='noticeTitle' class='form-control' rows='2' readonly style='resize: none;'>$notice_title</textarea>
                                                        </div>
                                                    </div>
                                                    <div class='col-md-6'>
                                                        <div class='mb-3'>
                                                            <label for='noticeSingleLine' class='form-label'>Single Line:</label>
                                                            <textarea id='noticeSingleLine' class='form-control' rows='2' readonly style='resize: none;'>$notice_single_line</textarea>
                                                        </div>
                                                    </div>
                                                    <div class='col-md-12'>
                                                        <div class='mb-3'>
                                                            <label for='noticeContent' class='form-label'>Content:</label>
                                                            <textarea id='noticeContent' class='form-control tinymce-editor' readonly>$notice_content</textarea>
                                                        </div>
                                                    </div>
                                                    <div class='col-md-12'>
                                                        <div class='mb-3'>
                                                            <label for='noticeExcerpt' class='form-label'>Excerpt:</label>
                                                            <textarea id='noticeExcerpt' class='form-control' rows='2' readonly style='resize: none;'>$notice_excerpt</textarea>
                                                        </div>
                                                    </div>
                                                    <div class='col-md-3'>
                                                        <div class='mb-3'>
                                                            <label for='noticeCategory' class='form-label'>Category:</label>
                                                            <input type='text' readonly value='$notice_category' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-4'>
                                                        <div class='mb-3'>
                                                            <label for='noticeBadge' class='form-label'>Badge:</label>
                                                            <input type='text' readonly value='$notice_badge' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-5'>
                                                        <div class='mb-3'>
                                                            <label for='noticeNumber' class='form-label'>Number:</label>
                                                            <input type='text' readonly value='$notice_number' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-3'>
                                                        <div class='mb-3'>
                                                            <label for='noticeUpdatedOn' class='form-label'>Updated On:</label>
                                                            <input type='text' readonly value='$notice_updated_on' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-3'>
                                                        <div class='mb-3'>
                                                            <label for='noticeStatus' class='form-label'>Status:</label>
                                                            <input type='text' readonly value='" . ucwords($notice_status) . "' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-3'>
                                                        <div class='mb-3'>
                                                            <label for='noticeMaterialTitle' class='form-label'>Material Title:</label>
                                                            <input type='text' readonly value='$notice_material_title' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-3'>
                                                        <div class='mb-3'>
                                                            <label for='noticeMaterial' class='form-label'>Material:</label>
                                                            <br>
                                                            <a href='" . get_site_option('dashboard_url') . "assets/uploads/documents/notices/$notice_material' target='_blank' class='btn btn-primary'>
                                                                View Document
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class='modal-footer d-flex justify-content-between'>
                                                <div>";
                                                    if ($notice_status === 'published') {
                                                        echo "
                                                            <button type='button' class='btn btn-warning' onclick=\"confirmStatusChange('notices', 'notice_id', $notice_id, 'notice_status', 'draft', 'notice_updated_on')\">
                                                                <i class='ri-draft-line'></i> Mark as Draft
                                                            </button>
                                                        ";
                                                    } elseif ($notice_status === 'draft') {
                                                        echo "
                                                            <button type='button' class='btn btn-success' onclick=\"confirmStatusChange('notices', 'notice_id', $notice_id, 'notice_status', 'published', 'notice_updated_on')\">
                                                                <i class='ri-checkbox-circle-line'></i> Mark as Published
                                                            </button>
                                                        ";
                                                    }
                                                    echo "
                                                    <button type='button' class='btn btn-danger' onclick=\"confirmStatusChange('notices', 'notice_id', $notice_id, 'notice_status', 'deleted', 'notice_updated_on')\">
                                                        <i class='ri-delete-bin-line'></i> Delete
                                                    </button>
                                                </div>
                                                <button type='button' class='btn btn-outline-secondary' data-bs-dismiss='modal'>Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                

                                <a href='" . get_site_option('dashboard_url') . "?page=edit-notice&notice_id=$notice_id' class='w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center'>
                                    <iconify-icon icon='lucide:edit'></iconify-icon>
                                </a>

                                <a href='javascript:void(0);' class='w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center' onclick=\"confirmStatusChange('notices', 'notice_id', $notice_id, 'notice_status', 'deleted', 'notice_updated_on')\">
                                    <iconify-icon icon='mingcute:delete-2-line'></iconify-icon>
                                </a>
                            </td>
                        </tr>
                    ";
                }
            }
        }
    }

    // Fetch single notice details
    function fetch_single_notice($notice_id) {
        global $con;

        $stmt = mysqli_prepare($con, "SELECT n.*, nc.notice_category_name AS notice_category_name FROM notices n
            INNER JOIN notice_categories nc ON n.notice_category = nc.notice_category_id
            WHERE n.notice_id = ? AND n.notice_status = 'published' AND nc.notice_category_status = 'active' LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $notice_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $notice = mysqli_fetch_assoc($result);

        if (!$notice) {
            return null;
        }

        return $notice;
    }

    // Fetch Additional Attachments for a Notice
    function fetch_additional_attachments($type, $notice_id) {
        global $con;

        $type = "add_attachment_" . strtolower($type) . "_id";

        $stmt = mysqli_prepare($con, "SELECT * FROM additional_attachments WHERE $type = ?");
        mysqli_stmt_bind_param($stmt, 'i', $notice_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            while ($attachment = mysqli_fetch_assoc($result)) {
                $attachment_id = $attachment['add_attachment_id'];
                $attachment_title = htmlspecialchars($attachment['add_attachment_title']);
                $attachment_material = htmlspecialchars($attachment['add_attachment_material']);
                $attachment_download_url = !empty($attachment_material) ? get_site_option('site_url') . 'assets/documents/' . $attachment_material : '';

                echo "
                    <a href='$attachment_download_url' class='btn-action btn-secondary' download>
                        <span><i class='fas fa-file-alt'></i></span>
                        <span>$attachment_title</span>
                    </a>
                ";
            }
        }
    }

    // Related notices
    function fetch_related_notices($current_notice_id, $limit = 3) {
        global $con;

        $stmt = mysqli_prepare($con, "SELECT n.*, nc.notice_category_name AS notice_category_name FROM notices n
            INNER JOIN notice_categories nc ON n.notice_category = nc.notice_category_id
            WHERE n.notice_status = 'published' AND n.notice_category = ? AND n.notice_id != ?
            ORDER BY n.notice_posted_on DESC LIMIT ?");
        mysqli_stmt_bind_param($stmt, 'iii', $category_id, $current_notice_id, $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 0) {
            return '<p class="text-muted">No related notices found.</p>';
        }

        while ($notice = mysqli_fetch_assoc($result)) {
            $notice_id = $notice['notice_id'];
            $notice_title = htmlspecialchars($notice['notice_title']);

            echo "
                <li class='related-item'>
                    <a href='" . get_site_option('site_url') . "notice-details/?notice_id=$notice_id' class='related-link'>
                        <span class='related-icon'><i class='fas fa-clipboard-check'></i></span>
                        <span class='related-text'>$notice_title</span>
                        <span class='related-arrow'><i class='fas fa-arrow-right'></i></span>
                    </a>
                </li>
            ";
        }
    }



    // FETCHING AGBMS FUNCTIONS
    function fetch_agbm($display_type = null, $status = 'published', $order_by = 'agbm_id', $sorting = 'DESC', $limit = null, $archive_only = false) {
        global $con;
        
        if (empty($display_type) || $display_type == NULL) {
            echo '<p class="text-muted">Display type is required in Function Call.</p>';
        }

        if ($display_type === 'homepage') {
            $query = "SELECT * FROM agbms WHERE agbm_status = ?";

            if ($archive_only === true) {
                $query .= " AND agbm_posted_on < DATE_SUB(NOW(), INTERVAL 1 YEAR)";
            } else {
                $query .= " AND agbm_posted_on >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
            }

            $query .= " ORDER BY $order_by $sorting";
            
            if ($limit !== null && is_numeric($limit)) {
                $query .= " LIMIT ?";
            }

            $stmt = mysqli_prepare($con, $query);

            if ($limit !== null && is_numeric($limit)) {
                mysqli_stmt_bind_param($stmt, 'si', $status, $limit);
            } else {
                mysqli_stmt_bind_param($stmt, 's', $status);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 0) {
                echo '<p class="text-muted">No AGBMs found.</p>';
            }

            while ($agbm = mysqli_fetch_assoc($result)) {
                $agbm_id = $agbm['agbm_id'];
                $agbm_number = htmlspecialchars($agbm['agbm_number']);
                $agbm_title = htmlspecialchars($agbm['agbm_title']);
                $agbm_date = date('d M, Y', strtotime($agbm['agbm_posted_on']));
                $agbm_single_line = htmlspecialchars($agbm['agbm_single_line']);
                $agbm_excerpt = nl2br(htmlspecialchars($agbm['agbm_excerpt']));
                $agbm_file = htmlspecialchars($agbm['agbm_material']);
                    if (!empty($agbm_file) || $agbm_file !== NULL) {
                        $download_url = get_site_option('dashboard_url') . 'assets/uploads/documents/agbms/' . $agbm_file;
                        $have_material = "<a href='$download_url' class='notice-btn' role='button' download><i class='fas fa-download'></i> Download PDF</a>";
                    } else {
                        $download_url = 'javascript:void(0);';
                        $have_material = "";
                    }
                
                echo "
                    <li class='announcement-item'>
                        <div class='announcement-header'>
                            <div class='announcement-main'>
                                <div class='announcement-title'>
                                    $agbm_title
                                </div>
                                <div class='announcement-meta'>
                                    Published on $agbm_date • $agbm_single_line
                                </div>
                            </div>
                        </div>
                        <div class='announcement-actions'>
                            <a href='./notice-details/?notice_id=$agbm_id' class='announcement-btn'>Read More</a>
                            $have_material
                        </div>
                    </li>
                ";
            }
        }

        if ($display_type === 'list_view') {
            $query = "SELECT * FROM agbms WHERE agbm_status = ?";

            if ($archive_only === true) {
                $query .= " AND agbm_posted_on < DATE_SUB(NOW(), INTERVAL 1 YEAR)";
            } else {
                $query .= " AND agbm_posted_on >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
            }

            $query .= " ORDER BY $order_by $sorting";
            
            if ($limit !== null && is_numeric($limit)) {
                $query .= " LIMIT ?";
            }

            $stmt = mysqli_prepare($con, $query);

            if ($limit !== null && is_numeric($limit)) {
                mysqli_stmt_bind_param($stmt, 'si', $status, $limit);
            } else {
                mysqli_stmt_bind_param($stmt, 's', $status);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 0) {
                echo '<p class="text-muted">No AGBMs found.</p>';
            }

            while ($agbm = mysqli_fetch_assoc($result)) {
                $agbm_id = $agbm['agbm_id'];
                $agbm_number = htmlspecialchars($agbm['agbm_number']);
                $agbm_title = htmlspecialchars($agbm['agbm_title']);
                $agbm_date = date('d M, Y', strtotime($agbm['agbm_posted_on']));
                $agbm_single_line = htmlspecialchars($agbm['agbm_single_line']);
                $agbm_excerpt = nl2br(htmlspecialchars($agbm['agbm_excerpt']));
                $agbm_file = htmlspecialchars($agbm['agbm_material']);
                    if (!empty($agbm_file) || $agbm_file !== NULL) {
                        $download_url = get_site_option('dashboard_url') . 'assets/uploads/documents/agbms/' . $agbm_file;
                        $have_material = "<a href='$download_url' class='notice-btn' role='button' download><i class='fas fa-download'></i> Download PDF</a>";
                    } else {
                        $download_url = 'javascript:void(0);';
                        $have_material = "";
                    }
                echo "
                    <article class='notice-card' data-keywords='$agbm_title $agbm_single_line $agbm_excerpt $agbm_number $agbm_date'>
                        <div class='notice-header'>
                            <div class='notice-title-group'>
                                <a href='" . get_site_option('site_url') . "agbm-details/?agbm_id=$agbm_id' class='notice-title-link' style='text-decoration:none;'><h4 class='notice-title' id='notice-1-title' style='cursor:pointer;'>$agbm_title</h4></a>
                                <div class='notice-meta'>
                                    <span class='notice-meta-item'><i class='fas fa-calendar-alt'></i> Published: $agbm_date</span>
                                    <span class='notice-meta-item'><i class='fas fa-user'></i> Issued by: Managing Committee</span>
                                    <span class='notice-meta-item'><i class='fas fa-file-alt'></i> Notice No: $agbm_number</span>
                                </div>
                            </div>
                        </div>
                        <div class='notice-content'>
                            $agbm_excerpt
                        </div>
                        <div class='notice-actions'>
                            <a href='" . get_site_option('site_url') . "agbm-details/?agbm_id=$agbm_id' class='notice-btn' role='button'><i class='fas fa-clipboard-list'></i> View Full Details</a>
                            $have_material
                        </div>
                    </article>
                ";
            }
        }

        if ($display_type === 'agbms-management') {
            $query = "SELECT a.*, om.* FROM agbms a
                LEFT JOIN office_members om ON a.agbm_posted_by = om.office_member_unique_id
                WHERE a.agbm_status != 'deleted' ORDER BY $order_by $sorting";

            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) <= 0) {
                echo "
                    <tr class='table-empty'>
                        <td class='text-center text-muted'>—</td>
                        <td class='text-muted'>No AGBMs found.</td>
                        <td class='text-muted'>&nbsp;</td>
                        <td class='text-muted'>&nbsp;</td>
                        <td class='text-muted'>&nbsp;</td>
                        <td class='text-muted'>&nbsp;</td>
                    </tr>
                ";
            } else {
                $i = 0;
                while($agbm = mysqli_fetch_assoc($result)) {
                    $i++;
                    $agbm_id = $agbm['agbm_id'];
                    $agbm_number = htmlspecialchars($agbm['agbm_number']);
                        if (strlen($agbm_number) > 15) {
                            $agbm_number_display = '...' . substr($agbm_number, -12);
                        } else {
                            $agbm_number_display = $agbm_number;
                        }

                    $agbm_title = htmlspecialchars($agbm['agbm_title']);
                        if (strlen($agbm_title) > 50) {
                            $notice_title_display = substr($agbm_title, 0, 47) . '...';
                        } else {
                            $notice_title_display = $agbm_title;
                        }

                    $agbm_material = htmlspecialchars($agbm['agbm_material']);
                        if (!empty($agbm_material) || $agbm_material !== NULL) {
                            $agbm_material_display = "
                                <a href='" . get_site_option('dashboard_url') . "assets/uploads/documents/agbms/$agbm_material' class='btn btn-primary btn-sm' target='_blank'>
                                    <iconify-icon icon='clarity:pop-out-line'></iconify-icon>
                                </a>
                            ";
                        } else {
                            $agbm_material_display = "N/A";
                        }
                    
                    $agbm_status = htmlspecialchars($agbm['agbm_status']);
                        if ($agbm_status === 'published') {
                            $agbm_status_badge = "
                                <button class='bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm' onclick=\"confirmStatusChange('agbms', 'agbm_id', $agbm_id, 'agbm_status', 'draft', 'agbm_updated_on')\"> " . ucwords(strtolower($agbm_status)) . "</button>
                            ";
                        } elseif ($agbm_status === 'draft') {
                            $agbm_status_badge = "
                                <button class='bg-warning-focus text-warning-main px-24 py-4 rounded-pill fw-medium text-sm' onclick=\"confirmStatusChange('agbms', 'agbm_id', $agbm_id, 'agbm_status', 'published', 'agbm_updated_on')\"> " . ucwords(strtolower($agbm_status)) . "</button>
                            ";
                        }

                    $agbm_posted_by_id = htmlspecialchars($agbm['agbm_posted_by']);
                    $agbm_posted_by_salutation = ucwords(strtolower($agbm['office_member_salutation']));
                    $agbm_posted_by_fullname = ucwords(strtolower($agbm['office_member_fullname']));
                    $agbm_posted_by_name = $agbm_posted_by_salutation . ' ' . $agbm_posted_by_fullname;

                    $agbm_posted_on = date('d M, Y h:i A', strtotime($agbm['agbm_posted_on']));
                    $agbm_single_line = htmlspecialchars($agbm['agbm_single_line']);
                    $agbm_video_link = htmlspecialchars($agbm['agbm_video_link']);
                    $agbm_content = $agbm['agbm_content'];
                    $agbm_excerpt = htmlspecialchars($agbm['agbm_excerpt']);
                    $agbm_material_title = htmlspecialchars($agbm['agbm_material_title']);
                    $agbm_updated_on = $agbm['agbm_updated_on'];
                        if (!empty($agbm_updated_on) || $agbm_updated_on !== NULL) {
                            $agbm_updated_on = date('d M, Y h:i A', strtotime($agbm['agbm_updated_on']));
                        } else {
                            $agbm_updated_on = 'N/A';
                        }

                    echo "
                        <tr>
                            <td class='text-center'>
                                $i
                            </td>
                            <td>$agbm_number_display</td>
                            <td>$notice_title_display</td>
                            <td>$agbm_material_display</td>
                            <td>$agbm_status_badge</td>
                            <td>
                                <a href='javascript:void(0)' class='w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center' data-bs-toggle='modal' data-bs-target='#viewAgbmModal$agbm_id'>
                                    <iconify-icon icon='iconamoon:eye-light'></iconify-icon>
                                </a>

                                <!--- View Notice Modal -->
                                <div class='modal fade' id='viewAgbmModal$agbm_id' tabindex='-1' aria-labelledby='viewAgbmModalLabel$agbm_id' aria-hidden='true'>
                                    <div class='modal-dialog modal-lg modal-dialog-centered'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='viewAgbmModalLabel$agbm_id'>Annual General Body Meeting Details</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                            </div>
                                            <div class='modal-body'>
                                                <div class='row'>
                                                    <div class='col-md-2'>
                                                        <div class='mb-3'>
                                                            <label for='agbmID' class='form-label'>ID:</label>
                                                            <input type='text' readonly value='$agbm_id' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-6'>
                                                        <div class='mb-3'>
                                                            <label for='agbmPostedBy' class='form-label'>Posted By:</label>
                                                            <input type='text' readonly value='$agbm_posted_by_name ($agbm_posted_by_id)' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-4'>
                                                        <div class='mb-3'>
                                                            <label for='agbmPostedOn' class='form-label'>Posted On:</label>
                                                            <input type='text' readonly value='$agbm_posted_on' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-6'>
                                                        <div class='mb-3'>
                                                            <label for='agbmTitle' class='form-label'>Title:</label>
                                                            <textarea id='agbmTitle' class='form-control' rows='2' readonly style='resize: none;'>$agbm_title</textarea>
                                                        </div>
                                                    </div>
                                                    <div class='col-md-6'>
                                                        <div class='mb-3'>
                                                            <label for='agbmSingleLine' class='form-label'>Single Line:</label>
                                                            <textarea id='agbmSingleLine' class='form-control' rows='2' readonly style='resize: none;'>$agbm_single_line</textarea>
                                                        </div>
                                                    </div>
                                                    <div class='col-md-12'>
                                                        <div class='mb-3'>
                                                            <label for='agbmVideo' class='form-label'>Video:</label>
                                                            <iframe src='$agbm_video_link' width='100%' height='480' allow='autoplay' allowfullscreen='' style='border: 1px solid #ddd; border-radius: 4px;'>
                                                            </iframe>
                                                        </div>
                                                    </div>
                                                    <div class='col-md-12'>
                                                        <div class='mb-3'>
                                                            <label for='agbmContent' class='form-label'>Content:</label>
                                                            <textarea id='agbmContent' class='form-control tinymce-editor' readonly>$agbm_content</textarea>
                                                        </div>
                                                    </div>
                                                    <div class='col-md-12'>
                                                        <div class='mb-3'>
                                                            <label for='agbmExcerpt' class='form-label'>Excerpt:</label>
                                                            <textarea id='agbmExcerpt' class='form-control' rows='2' readonly style='resize: none;'>$agbm_excerpt</textarea>
                                                        </div>
                                                    </div>
                                                    <div class='col-md-6'>
                                                        <div class='mb-3'>
                                                            <label for='agbmMaterialTitle' class='form-label'>Material Title:</label>
                                                            <input type='text' readonly value='$agbm_material_title' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-3'>
                                                        <div class='mb-3'>
                                                            <label for='agbmMaterial' class='form-label'>Material:</label>
                                                            <br>
                                                            <a href='" . get_site_option('dashboard_url') . "assets/uploads/documents/notices/$agbm_material' target='_blank' class='btn btn-primary'>
                                                                View Document
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class='col-md-3'>
                                                        <div class='mb-3'>
                                                            <label for='agbmStatus' class='form-label'>Status:</label>
                                                            <input type='text' readonly value='" . ucwords($agbm_status) . "' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-6'>
                                                        <div class='mb-3'>
                                                            <label for='agbmNumber' class='form-label'>Number:</label>
                                                            <input type='text' readonly value='$agbm_number' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-6'>
                                                        <div class='mb-3'>
                                                            <label for='agbmUpdatedOn' class='form-label'>Updated On:</label>
                                                            <input type='text' readonly value='$agbm_updated_on' class='form-control' />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class='modal-footer d-flex justify-content-between'>
                                                <div>";
                                                    if ($agbm_status === 'published') {
                                                        echo "
                                                            <button type='button' class='btn btn-warning' onclick=\"confirmStatusChange('agbms', 'agbm_id', $agbm_id, 'agbm_status', 'draft', 'agbm_updated_on')\">
                                                                <i class='ri-draft-line'></i> Mark as Draft
                                                            </button>
                                                        ";
                                                    } elseif ($agbm_status === 'draft') {
                                                        echo "
                                                            <button type='button' class='btn btn-success' onclick=\"confirmStatusChange('agbms', 'agbm_id', $agbm_id, 'agbm_status', 'published', 'agbm_updated_on')\">
                                                                <i class='ri-checkbox-circle-line'></i> Mark as Published
                                                            </button>
                                                        ";
                                                    }
                                                    echo "
                                                    <button type='button' class='btn btn-danger' onclick=\"confirmStatusChange('agbms', 'agbm_id', $agbm_id, 'agbm_status', 'deleted', 'agbm_updated_on')\">
                                                        <i class='ri-delete-bin-line'></i> Delete
                                                    </button>
                                                </div>
                                                <button type='button' class='btn btn-outline-secondary' data-bs-dismiss='modal'>Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                

                                <a href='" . get_site_option('dashboard_url') . "?page=edit-agbm&agbm_id=$agbm_id' class='w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center'>
                                    <iconify-icon icon='lucide:edit'></iconify-icon>
                                </a>

                                <a href='javascript:void(0);' class='w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center' onclick=\"confirmStatusChange('agbms', 'agbm_id', $agbm_id, 'agbm_status', 'deleted', 'agbm_updated_on')\">
                                    <iconify-icon icon='mingcute:delete-2-line'></iconify-icon>
                                </a>
                            </td>
                        </tr>
                    ";
                }
            }
        }
    }

    // Fetch single agbm details
    function fetch_single_agbm($agbm_id) {
        global $con;

        $stmt = mysqli_prepare($con, "SELECT * FROM agbms WHERE agbm_id = ? AND agbm_status = 'published' LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $agbm_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $agbm = mysqli_fetch_assoc($result);
        if (!$agbm) {
            return null;
        }

        return $agbm;
    }
    
    // Fetch single Member details
    function fetch_single_member($member_email) {
        global $con;

        $stmt = mysqli_prepare($con, "SELECT * FROM members WHERE member_email = ? AND member_status = 'active' LIMIT 0, 1");
        mysqli_stmt_bind_param($stmt, 's', $member_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $member = mysqli_fetch_assoc($result);
        if (!$member) {
            return null;
        }

        return $member;
    }

    // Format Block and Floor of Member
    function format_block_floor_label($block, $flat) {
        $block = trim((string) $block);
        $flat = trim((string) $flat);

        $ordinal = function (int $n): string {
            $mod100 = $n % 100;
            if ($mod100 >= 11 && $mod100 <= 13) {
                return $n . 'th';
            }

            switch ($n % 10) {
                case 1:
                    return $n . 'st';
                case 2:
                    return $n . 'nd';
                case 3:
                    return $n . 'rd';
                default:
                    return $n . 'th';
            }
        };

        $floorLabel = '';
        if ($flat !== '') {
            $flatDigits = ltrim(preg_replace('/\D+/', '', $flat), '0');
            $firstDigit = $flatDigits !== '' ? (int) substr($flatDigits, 0, 1) : 0;
            if ($firstDigit > 0) {
                $floorLabel = $ordinal($firstDigit) . ' Floor';
            }
        }

        $parts = [];
        if ($block !== '') {
            $parts[] = 'Block ' . htmlspecialchars($block, ENT_QUOTES, 'UTF-8');
        }
        if ($floorLabel !== '') {
            $parts[] = htmlspecialchars($floorLabel, ENT_QUOTES, 'UTF-8');
        }

        $label = implode(', ', $parts);
        return $label !== '' ? $label : 'N/A';
    }

    // BILLS FUNCTIONS
    function load_bills($display_type = null, $order_by = 'bill_id', $sorting = 'DESC', $limit = null) {
        global $con;
        
        if (empty($display_type) || $display_type == NULL) {
            echo '<p class="text-muted">Display type is required in Function Call.</p>';
        }

        if ($display_type === 'bills-management') {
            $query = "SELECT b.*, m.*, om.* FROM bills b
                INNER JOIN members m ON b.bill_for_member = m.member_unique_id
                LEFT JOIN office_members om ON b.bill_added_by = om.office_member_unique_id
                WHERE b.bill_status != 'deleted' AND m.member_status != 'deleted'
                ORDER BY $order_by $sorting";

            if ($limit !== null && is_numeric($limit)) {
                $query .= " LIMIT ?";
            }

            $stmt = mysqli_prepare($con, $query);

            if ($limit !== null && is_numeric($limit)) {
                mysqli_stmt_bind_param($stmt, 'i', $limit);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) <= 0) {
                echo "
                    <tr class='table-empty'>
                        <td class='text-center text-muted'>—</td>
                        <td class='text-muted'>No bills found.</td>
                        <td class='text-muted'>&nbsp;</td>
                        <td class='text-muted'>&nbsp;</td>
                        <td class='text-muted'>&nbsp;</td>
                        <td class='text-muted'>&nbsp;</td>
                    </tr>
                ";
            } else {
                $i = 0;
                while ($bill = mysqli_fetch_assoc($result)) {
                    $i++;
                    $bill_id = $bill['bill_id'];
                    $member_salutation = ucwords(strtolower(htmlspecialchars($bill['member_salutation'])));
                    $member_fullname = ucwords(strtolower(htmlspecialchars($bill['member_fullname'])));
                        if (strlen($member_fullname) > 30) {
                            $member_fullname = substr($member_fullname, 0, 27) . '...';
                        } else {
                            $member_fullname = $member_fullname;
                        }
                    $member_block = htmlspecialchars($bill['member_block']);
                    $member_flat = htmlspecialchars($bill['member_flat_number']);
                        $member_display = "(" . $member_block . "-" . $member_flat . ") " . $member_salutation . " " . $member_fullname;
                    
                    $bill_file = htmlspecialchars($bill['bill_file']);
                    $bill_month = date('F, Y', strtotime($bill['bill_for_month']));
                    $bill_status = htmlspecialchars($bill['bill_status']);
                        if ($bill_status === 'pending') {
                            $bill_status_badge = "
                                <button class='bg-warning-focus text-warning-main px-24 py-4 rounded-pill fw-medium text-sm' onclick=\"confirmStatusChange('bills', 'bill_id', $bill_id, 'bill_status', 'paid', 'bill_updated_on')\"> " . ucwords(strtolower($bill_status)) . "</button>
                            ";
                        } elseif ($bill_status === 'paid') {
                            $bill_status_badge = "
                                <button class='bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm' onclick=\"confirmStatusChange('bills', 'bill_id', $bill_id, 'bill_status', 'cancelled', 'bill_updated_on')\"> " . ucwords(strtolower($bill_status)) . "</button>
                            ";
                        } elseif ($bill_status === 'cancelled') {
                            $bill_status_badge = "
                                <button class='bg-secondary-focus text-danger-main px-24 py-4 rounded-pill fw-medium text-sm' onclick=\"confirmStatusChange('bills', 'bill_id', $bill_id, 'bill_status', 'pending', 'bill_updated_on')\"> " . ucwords(strtolower($bill_status)) . "</button>
                            ";
                        }
                    $bill_added_by_id = htmlspecialchars($bill['bill_added_by']);
                    $bill_added_by_salutation = ucwords(strtolower($bill['office_member_salutation']));
                    $bill_added_by_fullname = ucwords(strtolower(htmlspecialchars($bill['office_member_fullname'])));
                    $bill_added_by_name = $bill_added_by_salutation . ' ' . $bill_added_by_fullname;

                    $bill_added_on = date('d M, Y h:i A', strtotime($bill['bill_added_on']));
                    $bill_due_date = date('d M, Y', strtotime($bill['bill_due_on']));
                    $bill_updated_on = $bill['bill_updated_on'];
                        if (!empty($bill_updated_on) || $bill_updated_on !== NULL) {
                            $bill_updated_on = date('d M, Y h:i A', strtotime($bill['bill_updated_on']));
                        } else {
                            $bill_updated_on = 'N/A';
                        }

                    echo "
                        <tr>
                            <td class='text-center'>
                                $i
                            </td>
                            <td>$member_display</td>
                            <td>
                                <a href='" . get_site_option('dashboard_url') . "assets/uploads/documents/bills/$bill_file' class='btn btn-primary btn-sm' target='_blank'>
                                    <iconify-icon icon='clarity:pop-out-line'></iconify-icon>
                                </a>
                            </td>
                            <td>$bill_month</td>
                            <td>$bill_status_badge</td>
                            <td>
                                <a href='javascript:void(0)' class='w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center' data-bs-toggle='modal' data-bs-target='#viewBillModal$bill_id'>
                                    <iconify-icon icon='iconamoon:eye-light'></iconify-icon>
                                </a>

                                <!--- View Bill Modal -->
                                <div class='modal fade' id='viewBillModal$bill_id' tabindex='-1' aria-labelledby='viewBillModalLabel$bill_id' aria-hidden='true'>
                                    <div class='modal-dialog modal-lg modal-dialog-centered'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='viewBillModalLabel$bill_id'>Bill Details</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                            </div>
                                            <div class='modal-body'>
                                                <div class='row'>
                                                    <div class='col-md-2'>
                                                        <div class='mb-3'>
                                                            <label for='billID' class='form-label'>ID:</label>
                                                            <input type='text' readonly value='$bill_id' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-6'>
                                                        <div class='mb-3'>
                                                            <label for='billAddedBy' class='form-label'>Added By:</label>
                                                            <input type='text' readonly value='$bill_added_by_name ($bill_added_by_id)' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-4'>
                                                        <div class='mb-3'>
                                                            <label for='billAddedOn' class='form-label'>Added On:</label>
                                                            <input type='text' readonly value='$bill_added_on' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-6'>
                                                        <div class='mb-3'>
                                                            <label for='billMember' class='form-label'>Member:</label>
                                                            <input type='text' readonly value='$member_display' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-6'>
                                                        <div class='mb-3'>
                                                            <label for='billMonth' class='form-label'>Month:</label>
                                                            <input type='text' readonly value='$bill_month' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-12'>
                                                        <div class='mb-3'>
                                                            <label for='billFile' class='form-label'>File:</label>
                                                            <iframe src='" . get_site_option('dashboard_url') . "assets/uploads/documents/bills/$bill_file' width='100%' height='600px' style='border: 1px solid #ccc;'></iframe>
                                                        </div>
                                                    </div>
                                                    <div class='col-md-4'>
                                                        <div class='mb-3'>
                                                            <label for='billDueDate' class='form-label'>Due Date:</label>
                                                            <input type='text' readonly value='$bill_due_date' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-3'>
                                                        <div class='mb-3'>
                                                            <label for='billStatus' class='form-label'>Status:</label>
                                                            <input type='text' readonly value='" . ucwords($bill_status) . "' class='form-control' />
                                                        </div>
                                                    </div>
                                                    <div class='col-md-5'>
                                                        <div class='mb-3'>
                                                            <label for='billUpdatedOn' class='form-label'>Updated On:</label>
                                                            <input type='text' readonly value='$bill_updated_on' class='form-control' />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class='modal-footer d-flex justify-content-between'>
                                                <div>";
                                                    if ($bill_status === 'pending') {
                                                        echo "
                                                            <button type='button' class='btn btn-success' onclick=\"confirmStatusChange('bills', 'bill_id', $bill_id, 'bill_status', 'paid', 'bill_updated_on')\">
                                                                <i class='ri-hand-coin-line'></i> Mark as Paid
                                                            </button>
                                                            <button type='button' class='btn btn-secondary' onclick=\"confirmStatusChange('bills', 'bill_id', $bill_id, 'bill_status', 'cancelled', 'bill_updated_on')\">
                                                                <i class='ri-close-circle-line'></i> Mark as Cancelled
                                                            </button>
                                                        ";
                                                    } elseif ($bill_status === 'paid') {
                                                        echo "
                                                            <button type='button' class='btn btn-warning' onclick=\"confirmStatusChange('bills', 'bill_id', $bill_id, 'bill_status', 'pending', 'bill_updated_on')\">
                                                                <i class='ri-error-warning-line'></i> Mark as Pending
                                                            </button>
                                                            <button type='button' class='btn btn-secondary' onclick=\"confirmStatusChange('bills', 'bill_id', $bill_id, 'bill_status', 'cancelled', 'bill_updated_on')\">
                                                                <i class='ri-close-circle-line'></i> Mark as Cancelled
                                                            </button>
                                                        ";
                                                    } elseif ($bill_status === 'cancelled') {
                                                        echo "
                                                            <button type='button' class='btn btn-warning' onclick=\"confirmStatusChange('bills', 'bill_id', $bill_id, 'bill_status', 'pending', 'bill_updated_on')\">
                                                                <i class='ri-error-warning-line'></i> Mark as Pending
                                                            </button>
                                                        ";
                                                    }
                                                    echo "
                                                    <button type='button' class='btn btn-danger' onclick=\"confirmStatusChange('bills', 'bill_id', $bill_id, 'bill_status', 'deleted', 'bill_updated_on')\">
                                                        <i class='ri-delete-bin-line'></i> Delete
                                                    </button>
                                                </div>
                                                <button type='button' class='btn btn-outline-secondary' data-bs-dismiss='modal'>Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                

                                <a href='" . get_site_option('dashboard_url') . "?page=edit-bill&bill_id=$bill_id' class='w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center'>
                                    <iconify-icon icon='lucide:edit'></iconify-icon>
                                </a>

                                <a href='javascript:void(0);' class='w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center' onclick=\"confirmStatusChange('bills', 'bill_id', $bill_id, 'bill_status', 'deleted', 'bill_updated_on')\">
                                    <iconify-icon icon='mingcute:delete-2-line'></iconify-icon>
                                </a>
                            </td>
                        </tr>
                    ";
                }
            }
        }
    }

    // Load bills for a specific member
    function load_this_member_bills($member_email) {
        global $con;

        $member = fetch_single_member($member_email);
        if (!$member) {
            return '<p class="text-muted">Member not found.</p>';
        }

        $member_unique_id = $member['member_unique_id'];

        $stmt = mysqli_prepare($con, "SELECT * FROM bills WHERE bill_for_member = ? ORDER BY bill_for_month DESC");
        mysqli_stmt_bind_param($stmt, 's', $member_unique_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) == 0) {
            return '<p class="text-muted">No bills found.</p>';
        }

        while ($bill = mysqli_fetch_assoc($result)) {
            $bill_id = $bill['bill_id'];
            $bill_month = date('F, Y', strtotime($bill['bill_for_month']));
            $bill_file = htmlspecialchars($bill['bill_file']);
            $bill_due_date = date('d M, Y', strtotime($bill['bill_due_on']));
            $bill_status = htmlspecialchars($bill['bill_status']);
                if ($bill_status === 'pending') {
                    $bill_status_badge = "<span class='badge warning'>Pending</span>";
                    $bill_button = "
                        <button class='btn btn-success' type='button' disabled> 
                            <i class='fas fa-credit-card'></i> 
                            <span>Pay Now (Coming Soon)</span> 
                        </button>
                    ";
                } elseif ($bill_status === 'paid') {
                    $bill_status_badge = "<span class='badge success'>Paid</span>";
                    $bill_button = "
                        <a href='javascript:void(0);' class='btn btn-outline' disabled> 
                            <i class='fas fa-download'></i> 
                            <span>Receipt (Coming Soon)</span> 
                        </a>
                    ";
                } elseif ($bill_status === 'cancelled') {
                    $bill_status_badge = "<span class='badge secondary'>Cancelled</span>";
                } elseif ($bill_status === 'deleted') {
                    $bill_status_badge = "<span class='badge danger'>Deleted</span>";
                }

                
            $bill_file = htmlspecialchars($bill['bill_file']);
            $download_url = !empty($bill_file) ? get_site_option('site_url') . 'assets/documents/bills/' . $bill_file : '';

            echo "
                <div class='bill-card'>
                    <div class='bill-info'>
                        <div class='bill-title'>
                            Bill - $bill_month
                        </div>
                        <div class='bill-meta'>
                            <div class='bill-meta-item'>
                                <i class='fas fa-calendar'></i> 
                                <span>Due: $bill_due_date</span>
                            </div>
                            <div class='bill-meta-item'>
                                <i class='fas fa-file-alt'></i> 
                                <span>Invoice: #SVN-2025-01-301 <a href='" . get_site_option('dashboard_url') . "assets/uploads/documents/bills/$bill_file' target='_blank'>View Bill</a></span>
                            </div>
                            <div class='bill-meta-item'>
                                $bill_status_badge
                            </div>
                        </div>
                    </div>
                    <div class='bill-actions'>
                        <!-- <div class='bill-amount'>
                            ₹7,500
                        </div> -->
                        $bill_button
                    </div>
                </div>
            ";
        }
    }

    function get_all_vendors($page = 1, $per_page = 20, $search = '', $city_filter = '', $category_filter = '') {
        global $con;

        // Calculate offset
        $offset = ($page - 1) * $per_page;

        // Build WHERE clause
        $where_conditions = ["vendor_status = 'active'"];
        $params = [];
        $types = '';

        if (!empty($search)) {
            $where_conditions[] = "(vendor_business_fullname LIKE ? OR vendor_business_tagline LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= 'ss';
        }

        if (!empty($city_filter)) {
            $where_conditions[] = "vendor_city = ?";
            $params[] = $city_filter;
            $types .= 's';
        }

        if (!empty($category_filter)) {
            $where_conditions[] = "vendor_category = ?";
            $params[] = $category_filter;
            $types .= 's';
        }

        $where_clause = implode(' AND ', $where_conditions);

        // Count total matching vendors
        $count_query = "SELECT COUNT(*) as total FROM vendors WHERE $where_clause";
        $count_stmt = mysqli_prepare($con, $count_query);
        
        if (!empty($params)) {
            mysqli_stmt_bind_param($count_stmt, $types, ...$params);
        }
        
        mysqli_stmt_execute($count_stmt);
        $count_result = mysqli_stmt_get_result($count_stmt);
        $total_row = mysqli_fetch_assoc($count_result);
        $total_vendors = $total_row['total'];
        mysqli_stmt_close($count_stmt);

        // Fetch vendors with pagination
        $query = "SELECT v.*, c.*, vc.* FROM vendors v LEFT JOIN cities c ON v.vendor_city = c.city_id LEFT JOIN vendor_categories vc ON v.vendor_category = vc.vendor_category_id WHERE $where_clause ORDER BY vendor_id DESC LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($con, $query);
        
        $params[] = $per_page;
        $params[] = $offset;
        $types .= 'ii';
        
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $vendors = [];
        while ($vendor = mysqli_fetch_assoc($result)) {
            $vendors[] = $vendor;
        }
        mysqli_stmt_close($stmt);

        return [
            'vendors' => $vendors,
            'total' => $total_vendors,
            'current_page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total_vendors / $per_page)
        ];
    }

    function get_vendor_filter_options($filter_type = 'city') {
        global $con;

        if ($filter_type === 'city') {
            $query = "SELECT city_id, city_name FROM cities WHERE city_status = 'active' ORDER BY city_name ASC";
            $result = mysqli_query($con, $query);
            $options = [];

            while ($row = mysqli_fetch_assoc($result)) {
                $options[] = [
                    'id' => $row['city_id'],
                    'name' => $row['city_name']
                ];
            }

            return $options;
        }

        if ($filter_type === 'category') {
            $query = "SELECT vendor_category_id, vendor_category_name FROM vendor_categories WHERE vendor_category_status = 'active' ORDER BY vendor_category_name ASC";
            $result = mysqli_query($con, $query);
            $options = [];

            while ($row = mysqli_fetch_assoc($result)) {
                $options[] = [
                    'id' => $row['vendor_category_id'],
                    'name' => $row['vendor_category_name']
                ];
            }

            return $options;
        }

        return [];
    }


    // Categories Pills Carousel
    function category_pills_carousel() {
        global $con;

        $stmt = mysqli_prepare($con, "SELECT * FROM vendor_categories WHERE vendor_category_status = 'active' ORDER BY vendor_category_name ASC");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) <= 0) {
            echo "<p class='text-muted'>No categories found.</p>";
            return;
        }

        $categories = [];
        while ($category = mysqli_fetch_assoc($result)) {
            $categories[] = $category;
        }

        // Output categories twice for seamless looping
        for ($loop = 0; $loop < 2; $loop++) {
            foreach ($categories as $category) {
                $category_id = $category['vendor_category_id'];
                $category_name = htmlspecialchars($category['vendor_category_name']);
                $category_icon = htmlspecialchars($category['vendor_category_icon']);

                echo "
                    <div class='category-pill'>
                        <i class='$category_icon' aria-hidden='true'></i>
                        <span>$category_name</span>
                    </div>
                ";
            }
        }
    }
?>