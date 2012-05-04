<?php
/** Define ABSPATH as this files directory */
define( 'ABSPATH', dirname(__FILE__) . '/../../../' );
include_once(ABSPATH . "wp-config.php");
include_once(ABSPATH . "wp-load.php");
include_once(ABSPATH . "wp-includes/wp-db.php");

global $wpdb;

$calendarMonth = $_GET["month"];
$calendarYear = $_GET["year"];
$FirstPost = -1;
$stuffToReturn = array();
$stuffToReturn["myMonth"] = $calendarMonth;
$stuffToReturn["myYear"] = $calendarYear;


// Get days with posts
$dayswithposts = $wpdb->get_results("SELECT DISTINCT DAYOFMONTH(post_date)
		FROM $wpdb->posts WHERE MONTH(post_date) = '$calendarMonth'
		AND YEAR(post_date) = '$calendarYear'
		AND post_type = 'post' AND post_status = 'publish'
		AND post_date < '" . current_time('mysql') . '\'', ARRAY_N);

if ( $dayswithposts ) {
    foreach ( (array) $dayswithposts as $daywith ) {
        $daywithpost[] = $daywith[0];
    }
} else {
    $daywithpost = array();
}

if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'camino') !== false || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'safari') !== false)
    $ak_title_separator = "\n";
else
    $ak_title_separator = ', ';

$ak_titles_for_day = array();
$ak_post_titles = $wpdb->get_results("SELECT post_title, DAYOFMONTH(post_date) as dom "
        ."FROM $wpdb->posts "
        ."WHERE YEAR(post_date) = '$calendarYear' "
        ."AND MONTH(post_date) = '$calendarMonth' "
        ."AND post_date < '".current_time('mysql')."' "
        ."AND post_type = 'post' AND post_status = 'publish'"
);
if ( $ak_post_titles ) {
    foreach ( (array) $ak_post_titles as $ak_post_title ) {

        $post_title = esc_attr( apply_filters( 'the_title', $ak_post_title->post_title ) );

        if ( empty($ak_titles_for_day['day_'.$ak_post_title->dom]) )
            $ak_titles_for_day['day_'.$ak_post_title->dom] = '';
        if ( empty($ak_titles_for_day["$ak_post_title->dom"]) ) // first one
            $ak_titles_for_day["$ak_post_title->dom"] = $post_title;
        else
            $ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . $post_title;
    }
}

$unixmonth = mktime(0, 0 , 0, $calendarMonth, 1, $calendarYear);
$daysinmonth = intval(date('t', $unixmonth));
for ( $day = 1; $day <= $daysinmonth; ++$day ) {
    if ( in_array($day, $daywithpost) ) { // any posts today?
//        echo '<a href="' . get_day_link($thisyear, $thismonth, $day) . "\" title=\"" . esc_attr($ak_titles_for_day[$day]) . "\">$day</a>";
        if($FirstPost == -1) {
            $FirstPost = $day;
        }
        $myDay = array();
        $myDay["id"] = $day;
        $myDay["title"] = esc_attr($ak_titles_for_day[$day]);
        $myDay["href"] = get_day_link($calendarYear, $calendarMonth, $day);
        $stuffToReturn["$day"] = $myDay;
    }
}

$stuffToReturn["FirstPost"] = $FirstPost;

//$myDay = array();
//$myDay["id"] = 15;
//$myDay["title"] = "Dummy title!";
//$myDay["href"] = "http://localhost/wordpressNB/?m=20100515";
//$stuffToReturn["15"] = $myDay;
//
//
////$day = array();
////$day["id"] = 18;
////$day["title"] = "Hello world!";
////$day["href"] = "http://localhost/wordpressNB/?m=20100518";
////$stuffToReturn["18"] = $day;
////$day = array();
////$day["id"] = 21;
////$day["title"] = "Post 1, Post 2";
////$day["href"] = "http://localhost/wordpressNB/?m=20100521";
////$stuffToReturn["21"] = $day;
echo json_encode($stuffToReturn);
?>
