<?php
/**
* Plugin Name: Dinna Issue Tracker
* Plugin URI: https://dinnaonline.com.au/
* Description: This plugin manages and maintains a list of issues that are raised by customers online.
* Version: 1.0
* Author: Witness Dinna
* Author URI: http://dinnaonline.com.au/
**/

defined( 'ABSPATH' ) or die( 'Ooops! You do not have access to this file.' );

// add_action( 'the_content', 'my_thank_you_text' );

// function my_thank_you_text ( $content ) {
//     return $content .= '<p>Thank you so much for reading our posts!</p>';
// }

class DinnaIssueTracker
{
	// function __construct() {
	// 	add_action('wp_footer', array( $this, 'bot'));
	// }

	function activate() {
	    global $wpdb;
	    $table = $wpdb->prefix."customer_issues";
	    $structure = "CREATE TABLE $table (
	        id INT(9) NOT NULL AUTO_INCREMENT,
	        customer_email VARCHAR(255) NOT NULL,
	        customer_name VARCHAR(255) NOT NULL,
	        customer_message TEXT NOT NULL,
	        issue_priority INT(2) DEFAULT 1,
		UNIQUE KEY id (id)
	    );";
	    $wpdb->query($structure);

	}

	// function bot() {
	//     global $wpdb;
	//     $browser_name = $_SERVER['HTTP_USER_AGENT'];
	//     $bots = $wpdb->get_results("SELECT * FROM ".
	//         $wpdb->prefix."bot_counter");
	 
	//     foreach($bots as $bot)
	//     {
	//         if(!stristr($bot->bot_mark, $browser_name))
	//         {
	//             $wpdb->query("UPDATE ".$wp->prefix."bot_counter 
	//                 SET bot_visits = bot_visits+1 WHERE id = ".$bot->id);
	 
	//             break;
	//         }
	//     }
	// }
}

if ( class_exists('DinnaIssueTracker')) {
	$dinna_issue_tracker = new DinnaIssueTracker();
}

// activation
register_activation_hook( __FILE__, array( $dinna_issue_tracker, 'activate'));

