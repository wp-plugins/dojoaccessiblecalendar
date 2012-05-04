<?php
/** Define ABSPATH as this files directory */
define( 'ABSPATH', dirname(__FILE__) . '/../../../' );
include_once(ABSPATH . "wp-config.php");
include_once(ABSPATH . "wp-load.php");
include_once(ABSPATH . "wp-includes/wp-db.php");

global $wpdb;

$previousOrNext = $_GET["previousOrNext"];
$calendarMonth = $_GET["month"];
$calendarYear = $_GET["year"];
$stuffToReturn = array();
$stuffToReturn["myMonth"] = $calendarMonth;
$stuffToReturn["myYear"] = $calendarYear;

$previous;
$next;
// Get the next and previous month and year with at least one post
if(strcmp($previousOrNext, "previous") == 0) {
    $previous = $wpdb->get_row("SELECT DISTINCT DAY(post_date) AS day, MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		WHERE post_date < '$calendarYear-$calendarMonth-01'
		AND post_type = 'post' AND post_status = 'publish'
			ORDER BY post_date DESC
			LIMIT 1");
} else if(strcmp($previousOrNext, "next") == 0) {
    $next = $wpdb->get_row("SELECT DISTINCT DAY(post_date) AS day, MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		WHERE post_date >	'$calendarYear-$calendarMonth-01'
		AND MONTH( post_date ) != MONTH( '$calendarYear-$calendarMonth-01' )
		AND post_type = 'post' AND post_status = 'publish'
			ORDER	BY post_date ASC
			LIMIT 1");
}

if ( $previous ) {
    $stuffToReturn["previousMonth"] = get_month_link($previous->year, $previous->month);
    $stuffToReturn["FirstPost"] = $previous->day;
}

if ( $next ) {
    $stuffToReturn["nextMonth"] = get_month_link($next->year, $next->month);
    $stuffToReturn["FirstPost"] = $next->day;
}
echo json_encode($stuffToReturn);
?>
