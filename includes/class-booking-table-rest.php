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
                'methods'             => 'GET',
                'callback'            => array($this, 'get_booking_data_by_id'),
                'permission_callback' => '__return_true'
            )
        );

        register_rest_route(
            'myapi/v1',
            '/booking',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'send_booking_data'),
                'permission_callback' => array($this, 'permission_call'),
                //'permission_callback' => '__return_true',

                'args' => array(

                    'resource_id'    => array(
                        'required'          => true,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => array($this, 'validate_resource')
                    ),

                    'requester_name' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => array($this, 'validate_requester_name')
                    ),

                    'requester_email' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_email',
                        'validate_callback' => array($this, 'validate_requester_email')
                    ),

                    'start_time' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => array($this, 'requester_start_time')
                    ),

                    'end_time' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => array($this, 'requester_end_time')
                    ),
                ),
            )
        );

        register_rest_route(
            'myapi/v1',
            '/booking/(?P<id>\d+)',
            array(
                'methods'             => array('PUT', 'PATCH'),
                'callback'            => array($this, 'update_booking_data'),
                //'permission_callback' => array($this, 'permission_call'),
                'permission_callback' => '__return_true'
            )
        );

        register_rest_route(
            'myapi/v1',
            '/booking/(?P<id>\d+)',
            array(
                'methods'             => 'DELETE',
                'callback'            => array($this, 'delete_booking_data'),
                //'permission_callback' => array($this, 'permission_call'),
                'permission_callback' => '__return_true'
            )
        );

        register_rest_route(
            'myapi/v1',
            '/booking/booked/(?P<rid>\d+)/(?P<date>\d{4}-\d{2}-\d{2})',
            array(
                'methods'  => 'GET',
                'callback' => array($this, 'get_booked_data')
            )
        );

        register_rest_route(
            'myapi/v1',
            '/booking/requests',
            array(
                'methods'  => 'GET',
                'callback' => array($this, 'get_requesters_data')
            )
        );
    }

    public function get_booked_data(WP_REST_Request $request)
    {
        global $wpdb;

        $resource_id = absint($request->get_param('rid'));
        $date = sanitize_text_field($request->get_param('date'));

        $table_name = $wpdb->prefix . 'bookings';

        $query = $wpdb->prepare(
            "SELECT *
                FROM {$table_name}
                WHERE DATE(date) = %s
                AND resource_id = %d
                AND status = %s",
            $date,
            $resource_id,
            'confirmed'
        );

        $results = $wpdb->get_results($query);

        $data = array();

        foreach ($results as $row) {
            $start = $row->start_time;
            $end   = $row->end_time;

            $start_time = date("h:i a", strtotime($start));
            $end_time   = date("h:i a", strtotime($end));
            
            $data[] = array(
                'id'              => $row->id,
                'resource_id'     => $row->resource_id,
                'requester_name'  => $row->requester_name,
                'requester_email' => $row->requester_email,
                'start_time'      => $start_time,
                'end_time'        => $end_time,
                'date'            => $row->date
            );
        }

        return new WP_REST_Response($data, 200);
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
                'date'            => $row->date,
                'start_time'      => $row->start_time,
                'end_time'        => $row->end_time,
                'status'          => $row->status,
                'created_at'      => $row->created_at
            );
        }
        return new WP_REST_Response($data, 200);
    }

    public function send_booking_data(WP_REST_Request $request)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bookings';

        $resource_id    = absint($request->get_param('resource_id'));
        $name           = sanitize_text_field($request->get_param('requester_name'));
        $start_time     = sanitize_text_field($request->get_param('start_time'));
        $end_time       = sanitize_text_field($request->get_param('end_time'));
        $email          = sanitize_email($request->get_param('requester_email'));
        $date           = sanitize_text_field($request->get_param('date'));

        $data = array(
            'resource_id'     => $resource_id,
            'requester_name'  => $name,
            'requester_email' => $email,
            'start_time'      => $start_time,
            'end_time'        => $end_time,
            'status'          => "pending",
            'created_at'      => current_time('mysql'),
            'date'            => $date
        );

        $format = array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s');

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

        $med = "";

        if ($start_time > "12:00:00") {
            $med = "PM";
            $start_time .= " $med";
        } else {
            $med = "AM";
            $start_time .= " $med";
        }

        if ($end_time > "12:00:00") {
            $med = "PM";
            $end_time .= " $med";
        } else {
            $med = "AM";
            $end_time .= " $med";
        }

        $admin_email = get_option('admin_email');

        $subject = 'New Booking Request Received';

        $message  = "A new booking request has been submitted.\n\n";
        $message .= "Booking ID: " . $wpdb->insert_id . "\n";
        $message .= "Requester Name: " . $name . "\n";
        $message .= "Requester Email: " . $email . "\n";
        $message .= "Resource ID: " . $resource_id . "\n";
        $message .= "Start Time: " . $start_time . "\n";
        $message .= "End Time: " . $end_time . "\n";
        $message .= "Date: " . $date . "\n";
        $message .= "Status: Pending\n\n";
        $message .= "Please log in to WordPress to review this request.";

        wp_mail($admin_email, $subject, $message);

        return new WP_REST_Response(
            array(
                'success'    => true,
                'message'    => 'Booking created successfully.',
                'booking_id' => $wpdb->insert_id,
            ),
            201
        );
    }

    public function get_booking_data_by_id(WP_REST_Request $request)
    {
        global $wpdb;

        $id         = $request->get_param('id');

        $table_name = $wpdb->prefix . 'bookings';

        $query      = $wpdb->prepare("SELECT * FROM {$table_name} WHERE id= %d", $id);

        $results    = $wpdb->get_results($query);

        $result     = $results[0];

        $data = array(
            'id'              => $result->id,
            'resource_id'     => $result->resource_id,
            'requester_name'  => $result->requester_name,
            'requester_email' => $result->requester_email,
            'date'            => $result->date,
            'start_time'      => $result->start_time,
            'end_time'        => $result->end_time,
            'status'          => $result->status
        );

        $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
        if (!$existing) {
            return new WP_REST_Response(array('success' => false, 'message' => 'Booking not found.'), 404);
        }

        return new WP_REST_Response($data, 200);
    }

    public function update_booking_data(WP_REST_Request $request)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bookings';

        $id = absint($request->get_param('id'));
        if (!$id) {
            return new WP_REST_Response(array('success' => false, 'message' => 'Invalid ID.'), 400);
        }

        $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
        if (!$existing) {
            return new WP_REST_Response(array('success' => false, 'message' => 'Booking not found.'), 404);
        }

        $data   = array();
        $format = array();

        if ($request->has_param('resource_id')) {
            $data['resource_id'] = absint($request->get_param('resource_id'));
            $format[] = '%d';
        }

        if ($request->has_param('requester_name')) {
            $data['requester_name'] = sanitize_text_field($request->get_param('requester_name'));
            $format[] = '%s';
        }

        if ($request->has_param('requester_email')) {
            $data['requester_email'] = sanitize_email($request->get_param('requester_email'));
            $format[] = '%s';
        }

        if ($request->has_param('start_time')) {
            $data['start_time'] = sanitize_text_field($request->get_param('start_time'));
            $format[] = '%s';
        }

        if ($request->has_param('end_time')) {
            $data['end_time'] = sanitize_text_field($request->get_param('end_time'));
            $format[] = '%s';
        }

        if ($request->has_param('status')) {
            $data['status'] = sanitize_text_field($request->get_param('status'));
            $format[] = '%s';

            $to = $existing->requester_email;
            $subject = 'Booking Status Updated';

            $status = sanitize_text_field($request->get_param('status'));

            $message = "";

            if ($status == "confirmed") {

                $message = "Hello " . $existing->requester_name . ",\n\n";
                $message .= "Great news! Your booking request has been confirmed.\n\n";
                $message .= "Your selected booking slot has been successfully reserved. We will contact you soon with any additional details regarding your booking.\n\n";
                $message .= "Thank you for choosing us.\n\n";
                $message .= "Best regards,\n";
                $message .= "Booking Team";
            } elseif ($status == "cancelled") {

                $message = "Hello " . $existing->requester_name . ",\n\n";
                $message .= "We regret to inform you that your booking request has been cancelled.\n\n";
                $message .= "We apologize for any inconvenience caused. Please try selecting another available time slot if you would still like to make a booking.\n\n";
                $message .= "Thank you for your understanding.\n\n";
                $message .= "Best regards,\n";
                $message .= "Booking Team";
            } elseif ($status == "expired") {

                $message = "Hello " . $existing->requester_name . ",\n\n";
                $message .= "We would like to inform you that your booking request has expired.\n\n";
                $message .= "Unfortunately, the booking was not confirmed within the required time. Please submit a new booking request for an available time slot if you would still like to proceed.\n\n";
                $message .= "Thank you for your understanding.\n\n";
                $message .= "Best regards,\n";
                $message .= "Booking Team";
            }

            wp_mail($to, $subject, $message);
        }

        $where        = array('id' => $id);
        $where_format = array('%d');


        $updated = $wpdb->update($table_name, $data, $where, $format, $where_format);

        if (false === $updated) {
            return new WP_REST_Response(array('success' => false, 'message' => 'Failed to update booking.'), 500);
        }

        return new WP_REST_Response(array(
            'success'    => true,
            'message'    => 'Booking updated successfully.',
            'booking_id' => $id,
        ), 200);
    }

    public function delete_booking_data(WP_REST_Request $request)
    {
        global $wpdb;


        $table_name = $wpdb->prefix . 'bookings';

        $id = absint($request->get_param('id'));

        if (!$id) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => 'Please provide a valid booking ID.',
                ),
                400
            );
        }


        $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

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
            array('id' => $id),
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
                'message'    => 'Booking deleted successfully.',
                'deleted_id' => $id,
            ),
            200
        );
    }

    public function get_requesters_data()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bookings';

        $query = $wpdb->prepare("
                    SELECT 
                        b.id,
                        p.post_title,
                        b.requester_name,
                        b.start_time,
                        b.end_time,
                        b.status,
                        b.date,
                        b.created_at,
                        b.requester_email
                    FROM $table_name AS b
                    LEFT JOIN wp_posts AS p
                    ON b.resource_id = p.ID
        ");

        $results = $wpdb->get_results($query);

        $data = array();

        foreach ($results as $row) {
            $data[] = array(
                'id'              => $row->id,
                'post_title'      => $row->post_title,
                'requester_name'  => $row->requester_name,
                'requester_email' => $row->requester_email,
                'date'            => $row->date,
                'start_time'      => $row->start_time,
                'end_time'        => $row->end_time,
                'status'          => $row->status,
                'created_at'      => $row->created_at
            );
        }
        return new WP_REST_Response($data, 200);
    }

    // Validation 

    public function validate_requester_name($value)
    {
        $len = strlen($value);

        if ($len < 2 || $len > 100) {
            return new WP_Error(
                'invalid_requester_name_length',
                'The requester name must be between 2 and 100 characters long.',
                array('status' => 400)
            );
        }
    }

    public function validate_resource($value)
    {
        if (!preg_match('/^\d+$/', $value)) {
            return new WP_Error(
                'invalid_resource_format',
                'Resource ID must be a numeric value containing only numbers.',
                array('status' => 400)
            );
        }

        return true;
    }

    public function validate_requester_email($value)
    {

        if (!is_email($value)) {
            return new WP_Error('invalid_email', 'Please provide a valid email address format.', array('status' => 400));
        }

        return true;
    }

    public function requester_start_time($value)
    {
        if (!strtotime($value)) {
            return new WP_Error('invalid_start_time', 'Start time must be a valid date and time.', array('status' => 400));
        }
        return true;
    }

    public function requester_end_time($value)
    {
        if (!strtotime($value)) {
            return new WP_Error('invalid_end_time', 'End time must be a valid date and time.', array('status' => 400));
        }

        return true;
    }

    public function permission_call()
    {
        return current_user_can('edit_others_posts');
    }
}
