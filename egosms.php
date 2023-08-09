<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/ABHarop
 * @since             1.0.0
 * @package           Egosms
 *
 * @wordpress-plugin
 * Plugin Name:       EgoSMS
 * Plugin URI:        https://github.com/ABHarop/egosms
 * Description:       The EgoSMS Plugin integrates the EgoSMS Bulk messaging platform to your WordPress website.
 * Version:           1.0.0
 * Author:            Arop Boniface
 * Author URI:        https://github.com/ABHarop
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       egosms
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EGOSMS_VERSION', '1.0.0' );
define('PLUGIN_PATH',plugin_dir_path(__FILE__));
define('PLUGIN_URL',plugin_dir_url(__FILE__));
define('PLUGIN',plugin_basename(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-egosms-activator.php
 */
function activate_egosms() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-egosms-activator.php';
	Egosms_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-egosms-deactivator.php
 */
function deactivate_egosms() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-egosms-deactivator.php';
	Egosms_Deactivator::deactivate();
}

function uninstall_egosms() {
	require_once plugin_dir_path( __FILE__ ) . 'uninstall.php';
}

register_activation_hook( __FILE__, 'activate_egosms' );
register_deactivation_hook( __FILE__, 'deactivate_egosms' );
register_uninstall_hook(__FILE__, 'uninstall_egosms');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-egosms.php';

// Display the egosms on the left panel
function egosms(){
    add_menu_page(
        __( 'EgoSMS', 'textdomain' ),
        'EgoSMS',
        'manage_options', //  The capability required for this menu to be displayed to the user.
        'EgoSMS',
        'egosms_page',
        PLUGIN_URL . '/assets/img/icon.png', 110,
    );
}

add_action( 'admin_menu','egosms' );

function send_message() {

    global $wpdb;
    $user_table = $wpdb->prefix . "egosms_user";
    $message_table = $wpdb->prefix . "egosms_messages";

    // Get the id of the last order received
    function get_last_order_id(){
        global $wpdb;
        $statuses = array_keys(wc_get_order_statuses());
        $statuses = implode( "','", $statuses );

        $last_order = $wpdb->get_col( "
        SELECT MAX(ID) FROM {$wpdb->prefix}posts
        WHERE post_type LIKE 'shop_order'
        AND post_status IN ('$statuses')
        " );
        return reset($last_order);

    }

    $order_id = get_last_order_id();

    // Function for handling phone numbers from different countries
    function get_phone_number(){

        $order = wc_get_order(get_last_order_id());
        $order_data  = $order->get_data();
        $country = $order_data['billing']['country'];
        $phone_number = $order_data['billing']['phone'];

        if($country == 'UG'){
            if($phone_number[0] == '0'){
                $sent_phone = substr_replace(substr($phone_number, 1), '256', 0, 0);
            }else{
                $sent_phone = $phone_number;
            }
        }else{
            $sent_phone = $phone_number;
        }

        return $sent_phone;
    }

    $result = $wpdb->get_row ( "SELECT username, password, sender_id, message FROM $user_table " ); 
    
    // Required parameters for EgoSMS
    $username = $result->username;
    $password = $result->password;
    $sender = $result->sender_id;
    $my_message = $result->message;
    $number = get_phone_number();
    $message = 'Your order No. is '.$order_id.'. '.$my_message;

    require_once plugin_dir_path( __FILE__ ) . 'includes/API.php';
 
    if(SendSMS($username, $password, $sender, $number, $message) == 'OK')
    {
        $message_status = 1;
        $wpdb->query("INSERT INTO $message_table(recipient, message, message_status) VALUES('$number', '$message', '$message_status')");
    }else{
        $message_status = 0;
        $wpdb->query("INSERT INTO $message_table(recipient, message, message_status) VALUES('$number', '$message', '$message_status')");
    
    }
 
 }

add_action( 'woocommerce_new_order', 'send_message', 1, 1 );

function egosms_page(){
    require_once 'pages/admin.php';
}


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_egosms() {

	$plugin = new Egosms();
	$plugin->run();

}
run_egosms();
