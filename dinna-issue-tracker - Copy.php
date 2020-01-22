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

// require_once(__DIR__ . '/includes/class-PHPFormBuilder.php');
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

}

if ( class_exists('DinnaIssueTracker')) {
	$dinna_issue_tracker = new DinnaIssueTracker();
}

// activation
register_activation_hook( __FILE__, array( $dinna_issue_tracker, 'activate'));

function registration_form( $customer_name, $customer_email, $customer_message, $issue_priority ) {
    echo '
    <style>
    div {
        margin-bottom:2px;
    }
     
    input{
        margin-bottom:4px;
    }
    </style>
    ';
 
    echo '
    <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
    <div>
    <label for="customer_name">Your Name <strong>*</strong></label>
    <input type="text" name="customer_name" value="' . ( isset( $_POST['customer_name'] ) ? $customer_name : null ) . '">
    </div>
     
    <div>
    <label for="customer_email">Your Email <strong>*</strong></label>
    <input type="text" name="customer_email" value="' . ( isset( $_POST['customer_email']) ? $customer_email : null ) . '">
    </div>
 
   	<div>
    <label for="bio">Issue Description</label>
    <textarea name="customer_message">' . ( isset( $_POST['customer_message']) ? $customer_message : null ) . '</textarea>
    </div>
     
    <div>
    <label for="issue_priority">Priority</label>
    <input type="text" name="issue_priority" value="' . ( isset( $_POST['issue_priority']) ? $issue_priority : null ) . '">
    </div>
     
    <input type="submit" name="submit" value="Submit"/>
    </form>
    ';
}

function registration_validation( $customer_name, $customer_email, $customer_message, $issue_priority )  {
	global $reg_errors;
	$reg_errors = new WP_Error;

	if ( empty( $customer_name ) || empty( $customer_email ) || empty( $customer_message ) ) {
    $reg_errors->add('field', 'Required form field is missing');
	}

	if ( !is_email( $customer_email ) ) {
    $reg_errors->add( 'email_invalid', 'Email is not valid' );
	}

	if ( is_wp_error( $reg_errors ) ) {
	 
	    foreach ( $reg_errors->get_error_messages() as $error ) {
	     
	        echo '<div>';
	        echo '<strong>ERROR</strong>:';
	        echo $error . '<br/>';
	        echo '</div>';
	    }
	         
	}
}

function complete_registration() {
    global $customer_name, $customer_email, $customer_message, $issue_priority;
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        $customerdata = array(
        'customer_name'    =>   $customer_name,
        'customer_email'   =>   $customer_email,
        'customer_message' =>   $customer_message,
        'issue_priority'   =>   $issue_priority,
        );
        $customer_issue = wp_insert_user( $customerdata );
        echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';   
    }
}

function custom_registration_function() {
    if ( isset($_POST['submit'] ) ) {
        registration_validation(
        $_POST['customer_name'],
        $_POST['customer_email'],
        $_POST['customer_message'],
        $_POST['issue_priority']
        );
         
        // sanitize user form input
        global $customer_name, $customer_email, $customer_message, $issue_priority;
        $customer_name	 =   sanitize_text_field( $_POST['customer_name'] );
        $customer_email	  =   sanitize_email( $_POST['customer_email'] );
        $issue_priority  =   sanitize_text_field( $_POST['issue_priority'] );
        $customer_message  =  esc_textarea( $_POST['customer_message'] );
 
        // call @function complete_registration to create the user
        // only when no WP_error is found
        complete_registration(
        $customer_name,
        $customer_email,
        $customer_message,
        $issue_priority
        );
    }
 
    registration_form(
        $customer_name,
        $customer_email,
        $customer_message,
        $issue_priority
        );
}

// Register a new shortcode: [cr_custom_registration]
add_shortcode( 'cr_custom_registration', 'custom_registration_shortcode' );
 
// The callback function that will replace [book]
function custom_registration_shortcode() {
    ob_start();
    custom_registration_function();
    return ob_get_clean();
}