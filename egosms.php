<?php

    /**
     * The plugin bootstrap file
     *
     * This file is read by WordPress to generate the plugin information in the plugin
     * admin area. This file also includes all of the dependencies used by the plugin,
     * registers the activation and deactivation functions, and defines a function
     * that starts the plugin.
     *
     * @link              https://github.com/Pahappa
     * @since             1.0.3
     * @package           Egosms
     *
     * @wordpress-plugin
     * Plugin Name:       EgoSMS
     * Plugin URI:        https://github.com/Pahappa/
     * Description:       The EgoSMS Plugin integrates the EgoSMS Bulk messaging platform to your WordPress website.
     * Version:           1.0.3
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
     * Start at version 1.0.3 and use SemVer - https://semver.org
     * Rename this for your plugin and update it as you release new versions.
     */
    define( 'EGOSMS_VERSION', '1.0.3' );
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
            'EgoSMS',        
            'EgoSMS',             
            'manage_options', 
            'egosms',    
            'message_page',
            PLUGIN_URL . '/assets/img/icon.png', 110,
        );

        // Add a submenu page under the "EgoSMS" menu
        add_submenu_page(
            'egosms',
            'Message Page',
            'Message',
            'manage_options',
            'message',
            'message_page'
        );

        add_submenu_page(
            'egosms',           // Parent menu slug
            'Template Page',    // Page title
            'Templates',        // Menu title
            'manage_options',   // Capability (who can access)
            'templates',        // Submenu slug
            'templates_page'    // Callback function to display the submenu page
        );

        add_submenu_page(
            'egosms',
            'History Page',
            'History',
            'manage_options',
            'history',
            'history_page'
        );

        add_submenu_page(
            'egosms',
            'Settings Page',
            'Settings',
            'manage_options',
            'settings',
            'settings_page'
        );

        add_submenu_page(
            'Template',
            'Single Page',
            'Template',
            'manage_options',
            'template',
            'single_template_page'
        );

        // Remove submenu add_menu_page
        global $submenu;
        unset( $submenu['egosms'][0] );

    }

    // Define callback functions to display the different pages
    function message_page() {
        require_once 'pages/message.php';
    }

    function templates_page() {
        require_once 'pages/templates.php';
    }

    function history_page() {
        require_once 'pages/history.php';
    }

    function settings_page() {
        require_once 'pages/settings.php';
    }

    function single_template_page() {
        require_once 'pages/template.php';
    }

    // Function for handling SMS notification
    function send_order_status_sms_notification( $order_id, $old_status, $new_status ) {

        global $wpdb;
        $user_table = $wpdb->prefix . "egosms_user";
        $message_table = $wpdb->prefix . "egosms_messages";
        $template_table = $wpdb->prefix . "egosms_template";

        // Get website domain name
        $website_url = get_home_url();
        $website_domain = parse_url($website_url, PHP_URL_HOST);

        // Get the order object
        $order = wc_get_order( $order_id );

        // Get the customer's phone number from the order
        $billing_customer_phone = $order->get_billing_phone();
        $billing_customer_last_name = $order->get_billing_last_name();
        $billing_amount = $order->get_total();

        // Define the message content based on the new order status
        switch ( $new_status ) {
            case 'processing':

                $st_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $template_table WHERE order_status = %s", 'processing'));
                $wc_order_message = $st_row->custom_message;

                if($wc_order_message <> '' || $wc_order_message <> NULL){ 
                    $update_message = str_replace(['[last_name]', '[order_number]', '[amount]'], [$billing_customer_last_name, $order_id, $billing_amount], $wc_order_message);
                }else{
                    $update_message = 'Hello '.$billing_customer_last_name.'. Great News! Your '.$website_domain.' order No. '.$order_id.' has been received and currently being processed. Thank you for shopping with us. ';
                }
                
                break;

            case 'pending':

                $st_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $template_table WHERE order_status = %s", 'pending'));
                $wc_order_message = $st_row->custom_message;

                if($wc_order_message <> '' || $wc_order_message <> NULL){   
                    $update_message = str_replace(['[last_name]', '[order_number]', '[amount]'], [$billing_customer_last_name, $order_id, $billing_amount], $wc_order_message);
                }else{
                    $update_message = 'Hello '.$billing_customer_last_name.', your '.$website_domain.' order No. '.$order_id.' is almost ready! To finalize the process, please complete your payment. Thank you for shopping with us.';
                }
                
                break;

            case 'refunded':

                $st_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $template_table WHERE order_status = %s", 'refunded'));
                $wc_order_message = $st_row->custom_message;

                if($wc_order_message <> '' || $wc_order_message <> NULL){   
                    $update_message = str_replace(['[last_name]', '[order_number]', '[amount]'], [$billing_customer_last_name, $order_id, $billing_amount], $wc_order_message);
                }else{
                    $update_message = 'Hello '.$billing_customer_last_name.', your '.$website_domain.' order No. '.$order_id.' payment has been refunded. We apologize for any inconvenience.';
                }
                
                break;

            case 'failed':

                $st_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $template_table WHERE order_status = %s", 'failed'));
                $wc_order_message = $st_row->custom_message;

                if($wc_order_message <> '' || $wc_order_message <> NULL){   
                    $update_message = str_replace(['[last_name]', '[order_number]', '[amount]'], [$billing_customer_last_name, $order_id, $billing_amount], $wc_order_message);
                }else{
                    $update_message = 'Hello '.$billing_customer_last_name.', your '.$website_domain.' order No. '.$order_id.' has failed. We apologize for any inconvenience.';
                }
                
                break;

            case 'draft':

                $st_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $template_table WHERE order_status = %s", 'draft'));
                $wc_order_message = $st_row->custom_message;

                if($wc_order_message <> '' || $wc_order_message <> NULL){   
                    $update_message = str_replace(['[last_name]', '[order_number]', '[amount]'], [$billing_customer_last_name, $order_id, $billing_amount], $wc_order_message);
                }else{
                    $update_message = 'Hello '.$billing_customer_last_name.', your '.$website_domain.' order No. '.$order_id.' is awaiting processing, please be patient.';
                }
            
                break;

            case 'on-hold':

                $st_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $template_table WHERE order_status = %s", 'on-hold'));
                $wc_order_message = $st_row->custom_message;

                if($wc_order_message <> '' || $wc_order_message <> NULL){   
                    $update_message = str_replace(['[last_name]', '[order_number]', '[amount]'], [$billing_customer_last_name, $order_id, $billing_amount], $wc_order_message);
                }else{
                    $update_message = 'Hello '.$billing_customer_last_name.'! We regret to inform you that your '.$website_domain.' order No. '.$order_id.' is currently on hold. Our team is working to resolve this issue promptly.';
                }
                
                break;

            case 'completed':
        
                $st_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $template_table WHERE order_status = %s", 'completed'));
                $wc_order_message = $st_row->custom_message;

                if($wc_order_message <> '' || $wc_order_message <> NULL){   
                    $update_message = str_replace(['[last_name]', '[order_number]', '[amount]'], [$billing_customer_last_name, $order_id, $billing_amount], $wc_order_message);
                }else{
                    $update_message = 'Hello '.$billing_customer_last_name.'. Fantastic news! Your '.$website_domain.' order No. '.$order_id.' is officially complete. Thank you for shopping with us - enjoy your purchase and feel free to reach out with any questions.';
                }

                break;

            case 'cancelled':

                $st_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $template_table WHERE order_status = %s", 'cancelled'));
                $wc_order_message = $st_row->custom_message;

                if($wc_order_message <> '' || $wc_order_message <> NULL){   
                    $update_message = str_replace(['[last_name]', '[order_number]', '[amount]'], [$billing_customer_last_name, $order_id, $billing_amount], $wc_order_message);
                }else{
                    $update_message = 'Hello '.$billing_customer_last_name.'. Regretfully, your '.$website_domain.' order No. '.$order_id.' has been cancelled. For help or future shopping, please reach out. We apologize for any inconvenience.';
                }
                
                break;

            default:
                return; // Don't send SMS for other statuses
        }

        // Retrieve EgoSMS details
        $result = $wpdb->get_row ( "SELECT username, password, sender_id FROM $user_table " ); 

        if($billing_customer_phone[0] == '0'){
            $customer_phone = substr_replace(substr($billing_customer_phone, 1), '256', 0, 0);
        }else{
            $customer_phone = $billing_customer_phone;
        }

        // Required parameters for EgoSMS
        $username = $result->username;
        $password = $result->password;
        $sender = $result->sender_id;
        $number = $customer_phone;
        $message = $update_message;

        // Send the SMS
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


    // Register all actions here
    add_action( 'admin_menu','egosms' );
    add_action( 'woocommerce_order_status_changed', 'send_order_status_sms_notification', 10, 3 );

    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.3
     */
    function run_egosms() {

        $plugin = new Egosms();
        $plugin->run();

    }
    run_egosms();
