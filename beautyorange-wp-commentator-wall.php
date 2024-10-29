<?php
/*
Plugin Name: Beauty Orange WordPress Commentator Wall
Plugin URI: http://www.beautyorange.com/beauty-orange-projects/beauty-orange-wordpress-commentator-wall/
Description: A plugin for WordPress, show the all commentators's avatar.
Author: leo
Version: 1.00
Author URI: http://www.beautyorange.com
*/

wp_register_style('beautyorange-wp-commentator-wall', WP_PLUGIN_URL . '/'.basename(dirname(__FILE__)).'/commentator-wall.css');

wp_enqueue_style('beautyorange-wp-commentator-wall');

function beautyorange_wp_commentator_wall_filter($content) {
	return preg_replace("|<pre\s+.*class\s*=\s*\"commentator-wall\">(.*?)</pre>|ise", 
		"'<div class=\"commentator-wall\">'.build_wall().'</div>'", $content);
}

function build_wall() {
	$output = '';
	$exclude_emails = array('xxx@xxx.com','');
	$imgsize = 40; //unit px
	$min_comment_nums = 1; //0 is unlimited

	//$cur_time_span = date('Y-m-d H:i:s', strtotime('-1 week'));
	$cur_time_span = date('Y-m-d H:i:s', strtotime('-3 Month'));
	//$cur_time_span = date('Y-m-d H:i:s', strtotime('-1 Year'));

	global $wpdb;
	$request = "SELECT count(comment_ID) comment_nums,comment_author, comment_author_email,comment_author_url FROM {$wpdb->prefix}comments where comment_date>'".$cur_time_span."' AND comment_type='' AND comment_approved=1 GROUP BY comment_author_email ORDER BY count(comment_ID) DESC ";

	$comments = $wpdb->get_results($request);
	foreach ($comments as $comment) {
		if (in_array($comment->comment_author_email, $exclude_emails) 
			|| ($min_comment_nums!=0 && $comment->comment_nums <$min_comment_nums))  continue;
		$output .= "<a href='".$comment->comment_author_url."' title='".$comment->comment_author." (".$comment->comment_nums." comments)'>".get_avatar($comment->comment_author_email,$imgsize)."</a>";

	}

	return $output;
}

add_filter('the_content', 'beautyorange_wp_commentator_wall_filter', 0);
?>
