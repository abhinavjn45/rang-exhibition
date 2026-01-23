<?php
function fetch_featured_events(mysqli $con): array {
    $events = [];
    $seenIds = [];

    $commonWhere = "event_status NOT IN ('completed', 'deleted')
        AND (c.city_status = 'active' OR c.city_status IS NULL)
        AND (v.venue_status = 'active' OR v.venue_status IS NULL)
        AND event_from >= NOW()";

    // 1) Find the nearest upcoming date
    $minDateSql = "SELECT MIN(event_from) AS min_date
        FROM events e
        LEFT JOIN cities c ON e.event_city = c.city_id
        LEFT JOIN venues v ON e.event_venue = v.venue_id
        WHERE $commonWhere";

    $minDate = null;
    if ($stmtMin = mysqli_prepare($con, $minDateSql)) {
        mysqli_stmt_execute($stmtMin);
        $resMin = mysqli_stmt_get_result($stmtMin);
        if ($resMin && ($row = mysqli_fetch_assoc($resMin)) && !empty($row['min_date'])) {
            $minDate = $row['min_date'];
        }
        mysqli_stmt_close($stmtMin);
    }

    // 1a) Include all events on the nearest upcoming date
    if ($minDate !== null) {
        $nearestSql = "SELECT e.*, c.*, v.*
            FROM events e
            LEFT JOIN cities c ON e.event_city = c.city_id
            LEFT JOIN venues v ON e.event_venue = v.venue_id
            WHERE $commonWhere AND event_from = ?
            ORDER BY event_id DESC";

        if ($stmtNearest = mysqli_prepare($con, $nearestSql)) {
            mysqli_stmt_bind_param($stmtNearest, 's', $minDate);
            mysqli_stmt_execute($stmtNearest);
            $resNearest = mysqli_stmt_get_result($stmtNearest);
            while ($resNearest && ($row = mysqli_fetch_assoc($resNearest))) {
                $id = (int)$row['event_id'];
                $seenIds[$id] = true;
                $events[] = $row;
            }
            mysqli_stmt_close($stmtNearest);
        }
    }

    // 2) Include any other events within the next 7 days (dedupe)
    $within7Sql = "SELECT e.*, c.*, v.*
        FROM events e
        LEFT JOIN cities c ON e.event_city = c.city_id
        LEFT JOIN venues v ON e.event_venue = v.venue_id
        WHERE $commonWhere
          AND event_from <= DATE_ADD(NOW(), INTERVAL 7 DAY)
        ORDER BY event_from ASC, event_id DESC";

    if ($stmtWin = mysqli_prepare($con, $within7Sql)) {
        mysqli_stmt_execute($stmtWin);
        $resWin = mysqli_stmt_get_result($stmtWin);
        while ($resWin && ($row = mysqli_fetch_assoc($resWin))) {
            $id = (int)$row['event_id'];
            if (isset($seenIds[$id])) {
                continue; // already added from the nearest-date group
            }
            $seenIds[$id] = true;
            $events[] = $row;
        }
        mysqli_stmt_close($stmtWin);
    }

    return $events;
}
?>
