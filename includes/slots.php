<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function agent_booking_generate_slots(
    WP_REST_Request $request
) {

    global $wpdb;

    $table =
        $wpdb->prefix . 'agent_booking_slots';
    $params =
    $request->get_json_params();

    $agents = [];
    $agent_id = intval($params['agent_id']);
    if ($agent_id == 0 && !class_exists('Groups_User')) {
        $agents[] = 1;
    }
    if ($agent_id == 0 && class_exists('Groups_User')) {
        $users = get_users();
        foreach ($users as $user) {
            $group_user = new Groups_User($user->ID);
            foreach ($group_user->__get('groups') as $group) {
                if ($group->name == 'booking_agent') {
                    $agents[] = $user->ID;
                    break;
                }
            }
        }
    }
    else {
        $agents[] = $agent_id;
    }

    $start_date = new DateTime('today');

    foreach ($agents as $agentId) {
        for ($d = 0; $d < 7; $d++) {

            $date = clone $start_date;

            $date->modify("+{$d} day");

            for ($hour = 9; $hour < 17; $hour++) {

                foreach ([0, 30] as $minute) {

                    $slot_start = clone $date;

                    $slot_start->setTime(
                        $hour,
                        $minute
                    );

                    $slot_end = clone $slot_start;

                    $slot_end->modify('+30 minutes');

                    $result = $wpdb->insert(
                        $table,
                        [
                            'agent_id' => $agentId,

                            'slot_start_utc' =>
                                $slot_start->format(
                                    'Y-m-d H:i:s'
                                ),

                            'slot_end_utc' =>
                                $slot_end->format(
                                    'Y-m-d H:i:s'
                                ),

                            'status' => 'FREE',

                            'max_bookings' => 1,

                            'created_at' =>
                                current_time(
                                    'mysql',
                                    true
                                ),

                            'updated_at' =>
                                current_time(
                                    'mysql',
                                    true
                                ),
                        ]
                    );

                    if ($result === false) {
                        error_log(
                            'INSERT ERROR: ' . $wpdb->last_error
                        );
                    }
                    
                }
            }
        }
    }
    return [
        'success' => true,
        'message' => 'Slot generation completed'
    ];
}

function agent_booking_generate_unique_slots(
    WP_REST_Request $request
) {

    global $wpdb;

    $table =
        $wpdb->prefix . 'agent_booking_slots';
    $params = $request->get_json_params();

    $agents = [];
    $agent_id = intval($params['agent_id']);
    if ($agent_id == 0 && !class_exists('Groups_User')) {
        $agents[] = 1;
    }
    if ($agent_id == 0 && class_exists('Groups_User')) {
        $users = get_users();
        foreach ($users as $user) {
            $group_user = new Groups_User($user->ID);
            foreach ($group_user->__get('groups') as $group) {
                if ($group->name == 'booking_agent') {
                    $agents[] = $user->ID;
                    break;
                }
            }
        }
    }
    else {
        $agents[] = $agent_id;
    }

    $range = explode(" - ", $params['range']);
    
    $start_date = new DateTime(
        trim($range[0]),
        new DateTimeZone('UTC')
    );
    $end_date = new DateTime(
        trim($range[1]),
        new DateTimeZone('UTC')
    );
    $diff = date_diff($start_date, $end_date);
    $days = intval($diff->format("%a"));

    $start_time = explode(":", $params['from']);
    $end_time = explode(":", $params['to']);
    $start_hour = intval($start_time[0]);
    $end_hour = intval($end_time[0]);

    $duration = intval($params['duration']);

    foreach ($agents as $agentId) {
        // Delete all FREE slots within the interval
        $wpdb->query(
            $wpdb->prepare(
                "
                DELETE FROM {$table}

                WHERE
                    agent_id = %d

                    AND status = 'FREE'

                    AND slot_start_utc >= %s

                    AND slot_start_utc <= %s
                ",
                $agentId,
                $start_date->format('Y-m-d 00:00:00'),
                $end_date->format('Y-m-d 23:59:59')
            )
        );

        // Create new slots with FREE state
        for ($d = 0; $d <= $days; $d++) {

            $date = clone $start_date;

            $date->modify("+{$d} day");

            for ($hour = $start_hour; $hour < $end_hour; $hour++) {

                for (
                    $minute = 0;
                    $minute < 60;
                    $minute += $duration
                ) {

                    $slot_start = clone $date;

                    $slot_start->setTime(
                        $hour,
                        $minute
                    );

                    $slot_end = clone $slot_start;

                    $slot_end->modify('+' . $duration . ' minutes');

                    $wpdb->query(
                        $wpdb->prepare(
                            "
                            INSERT IGNORE INTO {$table}
                            (
                                agent_id,
                                slot_start_utc,
                                slot_end_utc,
                                status,
                                max_bookings,
                                created_at,
                                updated_at
                            )

                            VALUES (
                                %d,
                                %s,
                                %s,
                                %s,
                                %d,
                                NOW(),
                                NOW()
                            )
                            ",
                            [
                                $agentId,
                                $slot_start->format(
                                    'Y-m-d H:i:s'
                                ),
                                $slot_end->format(
                                    'Y-m-d H:i:s'
                                ),
                                'FREE',
                                1
                            ]
                        )
                    );
                    
                }
            }
        }
    }
    return [
        'success' => true,
        'message' => 'Unique Slot generation completed'
    ];
}
function agent_booking_get_slot_color(
    $status
) {

    switch($status) {

        case 'FREE':
            return '#4caf50';

        case 'BOOKED':
            return '#f44336';

        case 'BLOCKED':
            return '#9e9e9e';
    }
}

function get_monogram(
    $title
) {
    $monogram = '';
    $names = explode(' ', $title);
    foreach ($names as $name) {
        $monogram = $monogram . substr($name, 0, 1);
    }
    return $monogram;
}
function agent_booking_calendar_events(
    WP_REST_Request $request
) {
    global $wpdb;
    $table =
        $wpdb->prefix . 'agent_booking_slots';
    $usertable =
        $wpdb->prefix . 'users';

    $agent_id = intval(
        $request->get_param(
            'agent_id'
        )
    );

    $result = $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT
                b.*,
                u.display_name

            FROM
                {$table} b

            JOIN
                {$usertable} u
                ON u.ID = b.agent_id

            WHERE
                b.slot_start_utc >= CURDATE()

                AND (
                    %d = 0
                    OR
                    b.agent_id = %d
                )

            ORDER BY
                b.slot_start_utc,
                b.agent_id
            ",
            $agent_id,
            $agent_id
        )
    );
    $events = [];

    foreach ($result as $row) {

        $events[] = [
            'title' => get_monogram($row->display_name),

            'start' => $row->slot_start_utc,

            'end' => $row->slot_end_utc,

            'color' => agent_booking_get_slot_color(
                $row->status
            ),

            'extendedProps' => [
                'slot_id' => $row->id,
                'status' => $row->status,
                'name' => $row->display_name
            ]
        ];
    }

    return $events;
}
function agent_booking_update_slot_status(
    WP_REST_Request $request
) {

    global $wpdb;

    $table =
        $wpdb->prefix . 'agent_booking_slots';
    $params =
        $request->get_json_params();

    $id =
        intval($params['id']);

    $status =
        sanitize_text_field(
            $params['status']
        );

    $wpdb->query(
        "
        UPDATE {$table}
        SET status = '{$status}'
        WHERE id = {$id}
        "
    );

    return [
        'success' => true,
        'message' => 'Slot update complet'
    ];
}