<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Közös render függvény
 */
function agent_booking_plugin_render( $atts = [] ) {

    agent_booking_enqueue_assets();

    ob_start();
    ?>

    <div id="agent-booking-calendar"></div>

    <?php

    return ob_get_clean();
}

function agent_booking_enqueue_assets() {

    static $loaded = false;

    if ($loaded) {
        return;
    }

    $loaded = true;

    wp_enqueue_script(
        'fullcalendar',
        plugin_dir_url(__FILE__) . '../assets/vendor/fullcalendar/index.global.min.js',
        [],
        '6.1.20',
        true
    );

    wp_enqueue_script(
        'agent-calendar-js',
        plugin_dir_url(__FILE__) . '../assets/js/calendar.js',
        ['fullcalendar'],
        filemtime(
            plugin_dir_path(__FILE__) .
            '../assets/js/calendar.js'
        ),
        true
    );

    wp_enqueue_script(
        'agent-booking-js',
        plugin_dir_url(__FILE__) . '../assets/js/booking.js',
        ['agent-calendar-js'],
        filemtime(
            plugin_dir_path(__FILE__) .
            '../assets/js/booking.js'
        ),
        true
    );

    wp_enqueue_style(
        'agent-booking-style',
        plugin_dir_url(__FILE__) . '../assets/css/style.css',
        [],
        '1.0'
    );
}

/**
 * Shortcode regisztráció
 */
function agent_booking_plugin_shortcode( $atts ) {
    return agent_booking_plugin_render( $atts );
}

add_shortcode(
    'agent_booking_calendar',
    'agent_booking_plugin_shortcode'
);