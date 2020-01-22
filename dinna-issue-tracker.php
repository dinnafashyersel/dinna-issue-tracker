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

function registration_form( $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio ) {
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
    <label for="username">Username <strong>*</strong></label>
    <input type="text" name="username" value="' . ( isset( $_POST['username'] ) ? $username : null ) . '">
    </div>
     
    <div>
    <label for="password">Password <strong>*</strong></label>
    <input type="password" name="password" value="' . ( isset( $_POST['password'] ) ? $password : null ) . '">
    </div>
     
    <div>
    <label for="email">Email <strong>*</strong></label>
    <input type="text" name="email" value="' . ( isset( $_POST['email']) ? $email : null ) . '">
    </div>
     
    <div>
    <label for="website">Website</label>
    <input type="text" name="website" value="' . ( isset( $_POST['website']) ? $website : null ) . '">
    </div>
     
    <div>
    <label for="firstname">First Name</label>
    <input type="text" name="fname" value="' . ( isset( $_POST['fname']) ? $first_name : null ) . '">
    </div>
     
    <div>
    <label for="website">Last Name</label>
    <input type="text" name="lname" value="' . ( isset( $_POST['lname']) ? $last_name : null ) . '">
    </div>
     
    <div>
    <label for="nickname">Nickname</label>
    <input type="text" name="nickname" value="' . ( isset( $_POST['nickname']) ? $nickname : null ) . '">
    </div>
     
    <div>
    <label for="bio">About / Bio</label>
    <textarea name="bio">' . ( isset( $_POST['bio']) ? $bio : null ) . '</textarea>
    </div>
    <input type="submit" name="submit" value="Register"/>
    </form>
    ';
}

function registration_validation( $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio )  {

	global $reg_errors;
	$reg_errors = new WP_Error;

	if ( empty( $username ) || empty( $password ) || empty( $email ) ) {
	    $reg_errors->add('field', 'Required form field is missing');
	}

	if ( 4 > strlen( $username ) ) {
	    $reg_errors->add( 'username_length', 'Username too short. At least 4 characters is required' );
	}

	if ( username_exists( $username ) )
	    $reg_errors->add('user_name', 'Sorry, that username already exists!');

	if ( ! validate_username( $username ) ) {
	    $reg_errors->add( 'username_invalid', 'Sorry, the username you entered is not valid' );
	}

	if ( 5 > strlen( $password ) ) {
	        $reg_errors->add( 'password', 'Password length must be greater than 5' );
	}

	if ( !is_email( $email ) ) {
	    $reg_errors->add( 'email_invalid', 'Email is not valid' );
	}

	if ( email_exists( $email ) ) {
	    $reg_errors->add( 'email', 'Email Already in use' );
	}

	if ( ! empty( $website ) ) {
	    if ( ! filter_var( $website, FILTER_VALIDATE_URL ) ) {
	        $reg_errors->add( 'website', 'Website is not a valid URL' );
	    }
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
    global $reg_errors, $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        $userdata = array(
        'user_login'    =>   $username,
        'user_email'    =>   $email,
        'user_pass'     =>   $password,
        'user_url'      =>   $website,
        'first_name'    =>   $first_name,
        'last_name'     =>   $last_name,
        'nickname'      =>   $nickname,
        'description'   =>   $bio,
        );
        $user = wp_insert_user( $userdata );
        echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';   
    }
}

function custom_registration_function() {
    if ( isset($_POST['submit'] ) ) {
        registration_validation(
        $_POST['username'],
        $_POST['password'],
        $_POST['email'],
        $_POST['website'],
        $_POST['fname'],
        $_POST['lname'],
        $_POST['nickname'],
        $_POST['bio']
        );
         
        // sanitize user form input
        global $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
        $username   =   sanitize_user( $_POST['username'] );
        $password   =   esc_attr( $_POST['password'] );
        $email      =   sanitize_email( $_POST['email'] );
        $website    =   esc_url( $_POST['website'] );
        $first_name =   sanitize_text_field( $_POST['fname'] );
        $last_name  =   sanitize_text_field( $_POST['lname'] );
        $nickname   =   sanitize_text_field( $_POST['nickname'] );
        $bio        =   esc_textarea( $_POST['bio'] );
 
        // call @function complete_registration to create the user
        // only when no WP_error is found
        complete_registration(
        $username,
        $password,
        $email,
        $website,
        $first_name,
        $last_name,
        $nickname,
        $bio
        );
    }
 
    registration_form(
        $username,
        $password,
        $email,
        $website,
        $first_name,
        $last_name,
        $nickname,
        $bio
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