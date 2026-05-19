<?php
/*
 * Plugin Name:       Agent Booking Plugin
 * Plugin URI:        https://github.com/sumegizoltan73/agent-booking-plugin
 * Description:       Agent booking with WordPress plugin.
 * Version:           0.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Zoltan Peter Sumegi & ChatGPT
 * Author URI:        https://www.sumegizoltanpeter.hu/
 * License:           MIT
 * License URI:       https://mit-license.org
 * Update URI:        https://programozo.info.hu/agent-booking-plugin/
 * Text Domain:       agent-booking-plugin
 * Domain Path:       languages
 * Requires Plugins:  
 */

//require_once __DIR__ . '/includes/block.php';

/**
 * custom option and settings
 */
function wporg_settings_init() {
	

}

/**
 * Register our wporg_settings_init to the admin_init action hook.
 */
add_action( 'admin_init', 'wporg_settings_init' );


add_action( 'plugins_loaded', 'agent_booking_plugin_load_textdomain' );

function agent_booking_plugin_load_textdomain() {
    load_plugin_textdomain(
        'agent-booking-plugin',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );
}

function agent_booking_install() {

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name =
        $wpdb->prefix . 'availability_slots';

    $sql = "
    CREATE TABLE $table_name (

        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

        agent_id BIGINT UNSIGNED NOT NULL,

        slot_start_utc DATETIME NOT NULL,
        slot_end_utc DATETIME NOT NULL,

        status VARCHAR(20) NOT NULL DEFAULT 'FREE',

        max_bookings INT NOT NULL DEFAULT 1,

        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,

        PRIMARY KEY  (id),

        KEY idx_agent_date (
            agent_id,
            slot_start_utc
        ),

        KEY idx_status (
            status
        )

    ) $charset_collate;
    ";

		$table_name2 =
        $wpdb->prefix . 'bookings';

		$sql2 = "
		CREATE TABLE $table_name2 (
				id BIGINT UNSIGNED AUTO_INCREMENT,

				slot_id BIGINT UNSIGNED NOT NULL,

				customer_name VARCHAR(255) NOT NULL,
				customer_email VARCHAR(255) NOT NULL,
				customer_phone VARCHAR(100),

				notes TEXT,

				status ENUM(
						'PENDING',
						'CONFIRMED',
						'CANCELLED',
						'NOSHOW'
				) NOT NULL DEFAULT 'CONFIRMED',

				created_at DATETIME NOT NULL,
				updated_at DATETIME NOT NULL,
				
				PRIMARY KEY  (id),

				KEY idx_slot (
						slot_id
				),

				KEY idx_customer_email (
						customer_email
				)
		) $charset_collate
		";


		$table_name3 =
        $wpdb->prefix . 'agent_weekly_rules';

		$sql3 = "
		CREATE TABLE $table_name3 (
				id BIGINT UNSIGNED AUTO_INCREMENT,

				agent_id BIGINT UNSIGNED NOT NULL,

				weekday TINYINT NOT NULL,

				start_time TIME NOT NULL,
				end_time TIME NOT NULL,

				slot_duration_minutes INT NOT NULL DEFAULT 30,

				is_active TINYINT(1) NOT NULL DEFAULT 1,

				created_at DATETIME NOT NULL,
				updated_at DATETIME NOT NULL,

				PRIMARY KEY  (id),

				KEY idx_agent_weekday (
						agent_id,
						weekday
				)
		) $charset_collate
		";

		$table_name4 =
        $wpdb->prefix . 'agent_days_off';
		$sql4 = "
		CREATE TABLE $table_name4 (
				id BIGINT UNSIGNED AUTO_INCREMENT,

				agent_id BIGINT UNSIGNED NOT NULL,

				off_start_utc DATETIME NOT NULL,
				off_end_utc DATETIME NOT NULL,

				reason VARCHAR(255),

				created_at DATETIME NOT NULL,

				PRIMARY KEY  (id),

				KEY idx_agent_off (
						agent_id,
						off_start_utc
				)
		) $charset_collate
		";

    require_once(
        ABSPATH . 'wp-admin/includes/upgrade.php'
    );

    dbDelta($sql);
    dbDelta($sql2);
    dbDelta($sql3);
    dbDelta($sql4);
}

register_activation_hook(
    __FILE__,
    'agent_booking_install'
);
