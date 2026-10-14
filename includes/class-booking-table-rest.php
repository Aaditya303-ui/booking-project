<?php

namespace Aj\Booking\Table;

use WP_REST_Request, WP_REST_Response, WP_Error;

class Booking_Table_Rest
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_cpt_routes'));
    }

    public function register_cpt_routes()
    {
        register_rest_route(
            'myapi/v1',
            '/bookings',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_booking_data'),
                'permission_callback' => '__return_true'
            )
        );

        register_rest_route(
            'myapi/v1',
            '/booking/(?P<id>\d+)',
            array(
                'methods' => 'GET',
                'callback'            => array($this, 'get_booking_data_by_id'),
                'permission_callback' => '__return_true'
            )
        );
    }

    public function get_booking_data()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bookings';

        $query = $wpdb->prepare("SELECT * FROM {$table_name} WHERE 1=1");

        $results = $wpdb->get_results($query);

        $data = array();

        foreach ($results as $row) {
            $data[] = array(
                'id'              => $row->id,
                'resource_id'     => $row->resource_id,
                'requester_name'  => $row->requester_name,
                'requester_email' => $row->requester_email,
                'start_time'      => $row->start_time,
                'end_time'        => $row->end_time,
                'status'          => $row->status
            );
        }
        return new WP_REST_Response($data, 200);
    }

    public function get_booking_data_by_id(WP_REST_Request $request)
    {
        global $wpdb;

        $id   = $request->get_param('id');

        $table_name = $wpdb->prefix . 'bookings';
        
        $query = $wpdb->prepare("SELECT * FROM {$table_name} WHERE id= %d",$id);

        $results = $wpdb->get_results($query);

        $data[] = array(
                'id'              => $results[0]->id,
                'resource_id'     => $results[0]->resource_id,
                'requester_name'  => $results[0]->requester_name,
                'requester_email' => $results[0]->requester_email,
                'start_time'      => $results[0]->start_time,
                'end_time'        => $results[0]->end_time,
                'status'          => $results[0]->status
            );

            return new WP_REST_Response($data,200);
    }
}
