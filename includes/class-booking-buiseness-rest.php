<?php

namespace Aj\Booking\Table\Buisness;

use WP_REST_Request, WP_REST_Response, WP_Error;

class Booking_Buisness_Rest
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_buisness_routes'));
    }

    public function register_buisness_routes()
    {
        register_rest_route(
            'myapi/v1',
            '/buisness-hours',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_buisness_data'),
                'permission_callback' => '__return_true'
            )
        );

        register_rest_route(
            'myapi/v1',
            '/buisness-hours/(?P<id>\d+)',
            array(
                'methods'             => array('PUT', 'PATCH'),
                'callback'            => array($this, 'update_buisness_data'),
                'permission_callback' => '__return_true'
            )
        );

        register_rest_route(
            'myapi/v1',
            '/buisness-hours/isclosed',
            array(
                'methods'  => 'GET',
                'callback' => array($this, 'get_isclose_data'),
                'permission_callback' => '__return_true'
            )
        );
    }

    public function get_buisness_data()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'business_hours';

        $query = $wpdb->prepare("SELECT * FROM {$table_name} WHERE 1=1");

        $results = $wpdb->get_results($query);

        $data = array();

        foreach ($results as $row) {
            $current_day = trim(strtolower($row->day_of_week));

            $open      = $row->open_time;
            $close     = $row->close_time;

            $open_time  = date("g:i A", strtotime($open));
            $close_time = date("g:i A", strtotime($close));

            $data[] = array(
                'id'         => $row->id,
                'day'        => $row->day_of_week,
                'open_time'  => $open_time,
                'close_time' => $close_time,
                'is_closed'  => $row->is_closed
            );
        }
        return new WP_REST_Response($data, 200);
    }

    public function get_isclose_data()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'business_hours';

        $query = $wpdb->prepare("SELECT * FROM {$table_name} WHERE is_closed = 1");

        $results = $wpdb->get_results($query);

        $data = array();

        foreach ($results as $row) {
            $current_day = trim(strtolower($row->day_of_week));

            $open      = $row->open_time;
            $close     = $row->close_time;

            $open_time  = date("g:i A", strtotime($open));
            $close_time = date("g:i A", strtotime($close));

            $data[] = array(
                'id'         => $row->id,
                'day'        => $row->day_of_week,
                'open_time'  => $open_time,
                'close_time' => $close_time,
                'is_closed'  => $row->is_closed
            );
        }
        return new WP_REST_Response($data, 200);
    }

    public function update_buisness_data(WP_REST_Request $request)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'business_hours';

        $id = absint($request->get_param('id'));

        if (!$id) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => 'Invalid ID.'
                ),
                400
            );
        }

        $data   = array();
        $format = array();

        if ($request->has_param('open_time')) {
            $data['open_time'] = sanitize_text_field($request->get_param('open_time'));
            $format[] = '%s';
        }

        if ($request->has_param('close_time')) {
            $data['close_time'] = sanitize_text_field($request->get_param('close_time'));
            $format[] = '%s';
        }

        if ($request->has_param('is_closed')) {
            $data['is_closed'] = absint($request->get_param('is_closed'));
            $format[] = '%d';
        }

        $where        = array('id' => $id);
        $where_format = array('%d');

        $updated = $wpdb->update($table_name, $data, $where, $format, $where_format);

        if (is_wp_error($updated)) {
            $data = $updated->get_error_message();
            error_log($data);
        }
        if (false === $updated) {
            return new WP_REST_Response(array('success' => false, 'message' => 'Failed to update booking.'), 500);
        }

        return new WP_REST_Response(array(
            'success'    => true,
            'message'    => 'updated successfully.',
            'booking_id' => $id,
        ), 200);
    }

    public function permission_call()
    {
        return current_user_can('edit_others_posts');
    }
}
