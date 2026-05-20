<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

register_rest_route(
    'agent-booking/v1',
    '/generate-slots',
    [
        'methods' => 'POST',
        'callback' => 'agent_booking_generate_slots',
        'permission_callback' => function () {
            return current_user_can(
                'manage_options'
            );
        }
    ]
);