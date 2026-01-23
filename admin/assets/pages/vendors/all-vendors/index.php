<!-- Vendors Sidebar & Grid Layout -->
<section class="vendors-layout-section" aria-labelledby="vendors-search-title">
	<!-- Mobile Filter Trigger Button -->
	<button type="button" class="search-mobile-trigger" aria-haspopup="dialog" aria-controls="vendorsFilterModal">
		<i class="fas fa-sliders-h" aria-hidden="true"></i>
		<span>Search &amp; Filter</span>
	</button>

	<div class="vendors-container">
		<!-- Sidebar: Search & Filter -->
		<aside class="vendors-sidebar" aria-labelledby="vendors-sidebar-title">
			<div class="vendors-sidebar-card">
				<div class="vendors-sidebar-header">
					<p class="search-eyebrow">Search &amp; Filter</p>
					<!-- Mobile close button (hidden on desktop) -->
					<button type="button" class="vendors-sidebar-close" aria-label="Close sidebar" style="display: none;">
						<i class="fas fa-times" aria-hidden="true"></i>
					</button>
				</div>

				<div class="vendors-search-controls" aria-label="Vendor search filters">
					<div class="search-actions-desktop">
					<div class="search-control">
						<i class="fas fa-search icon-left" aria-hidden="true"></i>
						<input type="text" id="searchInput" placeholder="Search vendors" aria-label="Search vendors" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
					</div>
					<div class="search-control">
						<select id="cityFilter" aria-label="Filter by city">
							<option value="">All Cities</option>
							<?php
								$cities = get_vendor_filter_options('city');
								$selectedCity = $_GET['city'] ?? '';
								foreach ($cities as $city) {
									$cityId = htmlspecialchars($city['id']);
									$cityName = htmlspecialchars($city['name']);
									$selected = ($selectedCity !== '' && $selectedCity == $cityId) ? 'selected' : '';
									echo "<option value='$cityId' $selected>$cityName</option>";
								}
							?>
						</select>
					</div>
					<div class="search-control">
						<select id="categoryFilter" aria-label="Filter by category">
							<option value="">All Categories</option>
							<?php
								$categories = get_vendor_filter_options('category');
								$selectedCategory = $_GET['category'] ?? '';
								foreach ($categories as $cat) {
									$catId = htmlspecialchars($cat['id']);
									$catName = htmlspecialchars($cat['name']);
									$selected = ($selectedCategory !== '' && $selectedCategory == $catId) ? 'selected' : '';
									echo "<option value='$catId' $selected>$catName</option>";
								}
							?>
						</select>
					</div>
					<button type="button" class="btn-search-apply" id="applyFilters">Search</button>
				</div>
			</div>
		</div>
	</aside>

	<!-- Main Content: Results Counter & Vendor Cards -->
	<main class="vendors-main-content">
		<!-- Results counter -->
		<?php
			// Get filters from URL
			$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
			$search = $_GET['search'] ?? '';
			$city_filter = $_GET['city'] ?? '';
			$category_filter = $_GET['category'] ?? '';

			// Fetch vendors
			$vendor_data = get_all_vendors($current_page, 20, $search, $city_filter, $category_filter);
			$vendors = $vendor_data['vendors'];
			$total_vendors = $vendor_data['total'];
			$total_pages = $vendor_data['total_pages'];
			$showing_from = $total_vendors > 0 ? (($current_page - 1) * 20) + 1 : 0;
			$showing_to = min($current_page * 20, $total_vendors);
		?>
		<div class="vendors-results-counter" aria-live="polite">
			<p class="vendors-counter-text">Showing <span class="counter-current"><?= $showing_from ?>-<?= $showing_to ?></span> of <span class="counter-total"><?= $total_vendors ?></span> Vendors</p>
		</div>

<!-- Mobile Filter Modal (structure only, no functionality) -->
<div class="vendors-filter-modal" id="vendorsFilterModal" role="dialog" aria-modal="true" aria-labelledby="vendorsFilterTitle" aria-hidden="true">
	<div class="vendors-filter-panel">
		<div class="vendors-filter-head">
			<h3 class="vendors-filter-title" id="vendorsFilterTitle">Search &amp; Filter</h3>
			<button type="button" class="vendors-filter-close" aria-label="Close filters">
				<i class="fas fa-times" aria-hidden="true"></i>
			</button>
		</div>

		<div class="vendors-filter-body">
			<div class="search-control">
				<label for="modalVendorSearch" class="visually-hidden">Search vendors</label>
				<i class="fas fa-search icon-left" aria-hidden="true"></i>
				<input id="modalVendorSearch" type="text" placeholder="Search vendors" aria-label="Search vendors (mobile)" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
			</div>
			<div class="search-control">
				<label for="modalCityFilter" class="visually-hidden">Filter by city</label>
				<select id="modalCityFilter" aria-label="Filter by city (mobile)">
					<option value="">All Cities</option>
					<?php
						$selectedCity = $_GET['city'] ?? '';
						foreach ($cities as $city) {
							$cityId = htmlspecialchars($city['id']);
							$cityName = htmlspecialchars($city['name']);
							$selected = ($selectedCity !== '' && $selectedCity == $cityId) ? 'selected' : '';
							echo "<option value='$cityId' $selected>$cityName</option>";
						}
					?>
				</select>
			</div>
			<div class="search-control">
				<label for="modalCategoryFilter" class="visually-hidden">Filter by category</label>
				<select id="modalCategoryFilter" aria-label="Filter by category (mobile)">
					<option value="">All Categories</option>
					<?php
						$selectedCategory = $_GET['category'] ?? '';
						foreach ($categories as $cat) {
							$catId = htmlspecialchars($cat['id']);
							$catName = htmlspecialchars($cat['name']);
							$selected = ($selectedCategory !== '' && $selectedCategory == $catId) ? 'selected' : '';
							echo "<option value='$catId' $selected>$catName</option>";
						}
					?>
				</select>
			</div>
		</div>

		<div class="vendors-filter-footer">
			<button type="button" class="btn-filter-clear" id="clearFilters">Clear</button>
			<button type="button" class="btn-filter-apply" id="applyMobileFilters">Apply</button>
		</div>
	</div>
</div>

<!-- Vendors Directory Grid -->
<section class="vendors-grid-section" aria-labelledby="vendors-grid-title">
	<div class="container-lg">
		<div class="vendors-grid-wrapper">
			<?php
				if (empty($vendors)) {
					echo '<div class="no-vendors-message"><p>No vendors found matching your criteria. Try adjusting your filters.</p></div>';
				} else {
					foreach ($vendors as $vendor) {
						$vendor_id = htmlspecialchars($vendor['vendor_id']);
						$vendor_name = htmlspecialchars($vendor['vendor_business_fullname']);
						$vendor_slug = htmlspecialchars($vendor['vendor_slug']);
						$vendor_logo = htmlspecialchars($vendor['vendor_logo']);
						$vendor_tagline = htmlspecialchars($vendor['vendor_business_tagline']);
						$vendor_city = htmlspecialchars($vendor['vendor_city']);
						$city_name = htmlspecialchars($vendor['city_name'] ?? 'Unknown City');
						$category_name = htmlspecialchars($vendor['vendor_category_name'] ?? 'Uncategorized');
						$vendor_link = get_site_option('site_url') . "vendors/?vendor=$vendor_slug";
						
						// Check if logo exists, otherwise use placeholder
						$logo_path = get_site_option('dashboard_url') . "assets/uploads/images/logos/$vendor_logo";
						if (empty($vendor_logo)) {
							$logo_path = "https://via.placeholder.com/200x200?text=" . urlencode(substr($vendor_name, 0, 1));
						}

						echo "
							<div class='vendor-card'>
								<div class='vendor-card-logo'>
									<img src='$logo_path' alt='$vendor_name Logo' class='vendor-logo-img'>
								</div>
								<div class='vendor-card-content'>
									<h3 class='vendor-card-name'>$vendor_name</h3>
									<p class='vendor-card-tagline'>$vendor_tagline</p>
									<p class='vendor-card-category'><i class='fas fa-tag'></i> $category_name</p>
									<p class='vendor-card-location'><i class='fas fa-map-marker-alt'></i> $city_name</p>

									<a href='$vendor_link' class='btn-vendor-visit'>
										<span>View Details</span>
										<i class='fas fa-arrow-right'></i>
									</a>
								</div>
							</div>
						";
					}
				}
			?>
		</div>

		<!-- Pagination -->
		<?php if ($total_pages > 1): ?>
		<div class="vendors-pagination">
			<?php
				// Build query string for pagination links
				$query_params = [];
				if (!empty($search)) $query_params['search'] = $search;
				if (!empty($city_filter)) $query_params['city'] = $city_filter;
				if (!empty($category_filter)) $query_params['category'] = $category_filter;
				
				$query_string = !empty($query_params) ? '&' . http_build_query($query_params) : '';

				// Previous button
				if ($current_page > 1) {
					echo '<a href="?page=' . ($current_page - 1) . $query_string . '" class="pagination-btn pagination-prev"><i class="fas fa-chevron-left"></i> Previous</a>';
				}

				// Page numbers
				echo '<div class="pagination-numbers">';
				
				// Always show first page
				if ($current_page > 3) {
					echo '<a href="?page=1' . $query_string . '" class="pagination-number">1</a>';
					if ($current_page > 4) {
						echo '<span class="pagination-dots">...</span>';
					}
				}

				// Show pages around current page
				for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++) {
					$active_class = ($i === $current_page) ? 'pagination-number-active' : '';
					echo '<a href="?page=' . $i . $query_string . '" class="pagination-number ' . $active_class . '">' . $i . '</a>';
				}

				// Always show last page
				if ($current_page < $total_pages - 2) {
					if ($current_page < $total_pages - 3) {
						echo '<span class="pagination-dots">...</span>';
					}
					echo '<a href="?page=' . $total_pages . $query_string . '" class="pagination-number">' . $total_pages . '</a>';
				}

				echo '</div>';

				// Next button
				if ($current_page < $total_pages) {
					echo '<a href="?page=' . ($current_page + 1) . $query_string . '" class="pagination-btn pagination-next">Next <i class="fas fa-chevron-right"></i></a>';
				}
			?>
		</div>
		<?php endif; ?>
	</section>
	</main>
</section>
<!-- CTA Section -->
<section class="gallery-cta-section">
    <div class="container-lg">
        <div class="cta-content">
            <h2 class="u-section-title cta-title">Want us in Your City?</h2>
            <p class="cta-subtitle">Help us bring our exhibitions to your city by sharing your interest with us.</p>
            <div class="cta-buttons">
                <a href="javascript:void(0);" class="u-btn-primary" id="openUploadModal">Suggest Your City</a>
                <a href="<?= get_site_option('site_url') ?>cities/" class="u-btn-secondary">Explore Upcoming Exhibitions</a>
            </div>
        </div>
    </div>
</section>