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

class DinnaIssueTracker
{
    // function __construct() {
    //  add_action('wp_footer', array( $this, 'bot'));
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
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
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

function html_form_code() {
    echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
    echo '<p>';
    echo 'Your Name (required) <br />';
    echo '<input type="text" name="customer_name" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["customer_name"] ) ? esc_attr( $_POST["customer_name"] ) : '' ) . '" size="40" />';
    echo '</p>';
    echo '<p>';
    echo 'Your Email (required) <br />';
    echo '<input type="email" name="customer_email" value="' . ( isset( $_POST["customer_email"] ) ? esc_attr( $_POST["customer_email"] ) : '' ) . '" size="40" />';
    echo '</p>';
    echo '<p>';
    echo 'Bug or Issue Description (required) <br />';
    echo '<textarea rows="10" cols="35" name="customer_message">' . ( isset( $_POST["customer_message"] ) ? esc_attr( $_POST["customer_message"] ) : '' ) . '</textarea>';
    echo '</p>';
    echo '<p>';
    echo 'Issue Priority (required) (1=low and 5=critical) <br />';
    echo '<input type="text" name="issue_priority" placeholder="1,2,3,4,5" pattern="[a-zA-Z ]+" value="' . ( isset( $_POST["issue_priority"] ) ? esc_attr( $_POST["issue_priority"] ) : '' ) . '" size="40" />';
    echo '</p>';
    echo '<p><input type="submit" name="cf-submitted" value="Send"/></p>';
    echo '</form>';
}

function save_customer_data() {
    global $wpdb;
    // if the submit button is clicked, send the email
    if ( isset( $_POST['cf-submitted'] ) ) {

        // sanitize form values
        $name    = sanitize_text_field( $_POST["customer_name"] );
        $email   = sanitize_email( $_POST["customer_email"] );
        $priority = sanitize_text_field( $_POST["issue_priority"] );
        $message = esc_textarea( $_POST["customer_message"] );

        $table_name = $wpdb->prefix . 'customer_issues';

        $wpdb->insert( 
            $table_name, 
            array( 
                'customer_name' => $name, 
                'customer_email' => $email, 
                'customer_message' => $message, 
                'issue_priority' => $priority, 
                'time' => current_time( 'mysql' ), 
            ) 
        );
    }
}

function form_shortcode() {
    ob_start();
    save_customer_data();
    html_form_code();

    return ob_get_clean();
}

add_shortcode( 'dinna_issue_form', 'form_shortcode' );

function issues_menu()
{
    global $wpdb;
    include 'issues-admin.php';
}
 
function issues_admin_actions()
{
    add_options_page("Customer Issues", "Customer Issues", 1, "Dinna-Issue-Tracker", "issues_menu");
}
 
add_action('admin_menu', 'issues_admin_actions');

function dinna_plugin_action_links( $links ) {

    $links = array_merge( array(
        '<a href="' . esc_url( admin_url( '/options-general.php?page=Dinna-Issue-Tracker' ) ) . '">' . __( 'View Issues', 'textdomain' ) . '</a>',
        '<a href="' . esc_url( admin_url( '/options-general.php' ) ) . '">' . __( 'Edit', 'textdomain' ) . '</a>'
    ), $links );

    return $links;

}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'dinna_plugin_action_links' );