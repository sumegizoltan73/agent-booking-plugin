<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Közös render függvény
 */
function agent_booking_plugin_render( $atts = [] ) {

    ob_start();
    ?>

    <div id="agent-booking-calendar"></div>

    <?php

    return ob_get_clean();
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