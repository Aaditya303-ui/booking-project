<?php

namespace Aj\Booking\Table\Blackout;

use DateTime;
use WP_REST_Request, WP_REST_Response, WP_Error;

class Booking_Blackout_Rest
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_cpt_routes'));
    }

    public function register_cpt_routes()
    {
        register_rest_route(
            'myapi/v1',
            '/blackout',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_blackout_dates'),
                'permission_callback' => '__return_true'
            )
        );

        register_rest_route(
            'myapi/v1',
            '/blackout',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'add_blackout_date'),
                'permission_callback' => '__return_true'
            )
        );

        register_rest_route(
            'myapi/v1',
            '/blackout/(?P<blackout_id>\d+)',
            array(
                'methods'             => 'DELETE',
                'callback'            => array($this, 'delete_blackout_date'),
                'permission_callback' => '__return_true'
            )
        );
    }

    public function get_blackout_dates()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'blackout';

        $query = $wpdb->prepare("SELECT * FROM {$table_name} WHERE 1=1");

        $results = $wpdb->get_results($query);

        $data = array();

        foreach ($results as $row) {
            $arr  = explode(" ", $row->blackout_date);
            $date = $arr[0];
            $new_date = new DateTime($date);
            $date_format = $new_date->format('l, F j, Y');

            $data[] = array(
                'blackout_id'       => $row->blackout_id,
                'blackout_date'     => $row->blackout_date,
                'reason'            => $row->reason,
                'date'              => $date_format,
                'exdate'            => $date
            );
        }
        return new WP_REST_Response($data, 200);
    }

    public function add_blackout_date(WP_REST_Request $request)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'blackout';

        $blackout_date     = sanitize_text_field($request->get_param('blackout_date'));
        $reason            = sanitize_text_field($request->get_param('reason'));

        $data = array(
            'blackout_date' => $blackout_date,
            'reason'        => $reason
        );

        $format = array('%s', '%s');

        $inserted = $wpdb->insert($table_name, $data, $format);

        if (false === $inserted) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => 'Failed to save booking. Please try again.',
                ),
                500
            );
        }

        return new WP_REST_Response(
            array(
                'success'    => true,
                'message'    => 'Blackout data created successfully.',
                'booking_id' => $wpdb->insert_id,
            ),
            201
        );
    }

    public function delete_blackout_date(WP_REST_Request $request)
    {
        global $wpdb;


        $table_name = $wpdb->prefix . 'blackout';

        $id = absint($request->get_param('blackout_id'));

        if (!$id) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => 'Please provide a valid blackout ID.',
                ),
                400
            );
        }


        $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE blackout_id = %d", $id));

        if (!$existing) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => 'Booking not found.',
                ),
                404
            );
        }


        $deleted = $wpdb->delete(
            $table_name,
            array('blackout_id' => $id),
        );


        if (false === $deleted) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => 'Failed to delete the booking from the database.',
                ),
                500
            );
        }

        return new WP_REST_Response(
            array(
                'success'    => true,
                'message'    => 'date deleted successfully.',
                'deleted_id' => $id,
            ),
            200
        );
    }
}
