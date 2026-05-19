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


add_action( 'plugins_loaded', 'agent_booking_plugin_load_textdomain' );

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/agents.php';
require_once __DIR__ . '/includes/cron.php';
require_once __DIR__ . '/includes/routes.php';
require_once __DIR__ . '/includes/shortcode.php';
require_once __DIR__ . '/includes/widget.php';


/**
 * Register our wporg_settings_init to the admin_init action hook.
 */
add_action( 'admin_init', 'wporg_settings_init' );

/**
 * custom option and settings
 */
function wporg_settings_init() {
	

}

/**
 * languages
 */
function agent_booking_plugin_load_textdomain() {
    load_plugin_textdomain(
        'agent-booking-plugin',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );
}

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_script(
        'fullcalendar',
        'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js',
        [],
        null,
        true
    );
	wp_enqueue_style(
        'agent-booking-admin-style',
        plugin_dir_url(__FILE__) . '/assets/css/admin.css?nocache=' . date("Ymd_His")
    );
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'fullcalendar',
        'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js',
        [],
        null,
        true
    );
    wp_enqueue_script(
        'agent-calendar-js',
        plugin_dir_url(__FILE__) . '/assets/js/calendar.js?nocache=' . date("Ymd_His"),
        ['jquery'],
        '1.0',
        true
    );
    wp_enqueue_script(
        'agent-booking-js',
        plugin_dir_url(__FILE__) . '/assets/js/booking.js?nocache=' . date("Ymd_His"),
        ['jquery'],
        '1.0',
        true
    );

	wp_enqueue_style(
        'agent-booking-style',
        plugin_dir_url(__FILE__) . '/assets/css/style.css?nocache=' . date("Ymd_His")
    );
});