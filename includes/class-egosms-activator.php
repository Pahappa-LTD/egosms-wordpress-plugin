<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/ABHarop
 * @since      1.0.0
 *
 * @package    Egosms
 * @subpackage Egosms/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Egosms
 * @subpackage Egosms/includes
 * @author     Arop Boniface <arop@pahappa.com>
 */
class Egosms_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$prefix = $wpdb->prefix;
		$egouser_tb = $prefix . "egosms_user";
		$message_tb = $prefix . "egosms_messages";

		//Check if tables exist. In case it's false we create it
		// Create a table for storing egosms user
		if($wpdb->get_var("SHOW TABLES LIKE '$egouser_tb'") !== $egouser_tb){
			$msql = "CREATE TABLE IF NOT EXISTS $egouser_tb(
				id mediumint unsigned NOT NULL PRIMARY KEY auto_increment,
				username VARCHAR(20),
				password VARCHAR(150),
				sender_id VARCHAR(50),
				message VARCHAR(150)
			)";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($msql);
		}

		// Create a table for storing messages
		if($wpdb->get_var("SHOW TABLES LIKE '$message_tb'") !== $message_tb){
			$msql = "CREATE TABLE IF NOT EXISTS $message_tb(
				id mediumint unsigned NOT NULL PRIMARY KEY auto_increment,
				send_date DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
				recipient VARCHAR(20),
				message text,
				message_status VARCHAR(1)
			)";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($msql);
		}

	}

}
