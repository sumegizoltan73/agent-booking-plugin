<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action(
    'admin_menu',
    'agent_booking_admin_menu'
);

function agent_booking_admin_menu() {

    add_menu_page(
        'Agent Booking',
        'Agent Booking',
        'manage_options',
        'agent-booking',
        'agent_booking_admin_page',
        'dashicons-calendar-alt',
        30
    );
}

function agent_booking_admin_page() {

    ?>

    <div class="wrap">

        <h1>Agent Booking</h1>

        <button id="generate-slots">
            Slotok generálása
        </button>

        <div id="agent-booking-admin-calendar"></div>

    </div>

    <?php
}

add_action(
    'admin_enqueue_scripts',
    'agent_booking_admin_assets'
);

function agent_booking_admin_assets($hook) {

    if ($hook !== 'toplevel_page_agent-booking') {
        return;
    }

    wp_enqueue_script(
        'fullcalendar',
        plugin_dir_url(__FILE__) . '../assets/vendor/fullcalendar/index.global.min.js',
        [],
        '6.1.20',
        true
    );

    wp_enqueue_script(
        'agent-admin-calendar-js',
        plugin_dir_url(__FILE__) . '../assets/js/admin.js',
        ['fullcalendar'],
        filemtime(
            plugin_dir_path(__FILE__) .
            '../assets/js/admin.js'
        ),
        true
    );
}