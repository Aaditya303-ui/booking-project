<?php

namespace Aj\Booking;

use WP_REST_Request, WP_REST_Response, WP_Error;

class Booking_CPT_Rest
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_cpt_routes'));
    }

    public function register_cpt_routes()
    {

        register_rest_route(
            'myapi/v1',
            '/resources',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_cpt_data'),
                'permission_callback' => '__return_true'
            )
        );

        register_rest_route(
            'myapi/v1',
            '/resource/(?P<id>\d+)',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_cpt_by_id'),
                'permission_callback' => '__return_true'
            )
        );

        register_rest_route(
            'myapi/v1',
            '/resource',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'add_cpt_data'),

                'permission_callback' => array($this, 'permission_call'),

                'args' => array(

                    'title' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => array($this, 'validate_title'),
                    ),

                    'content' => array(
                        'required'                => true,
                        'sanitize_callback'       => 'sanitize_textarea_field',
                        'validate_callback'       => array($this, 'validate_content'),
                    ),

                    'capacity' => array(
                        'required'          => true,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => array($this, 'validate_capacity'),
                    ),
                )
            )
        );

        register_rest_route(
            'myapi/v1',
            '/resource/(?P<id>\d+)',
            array(
                'methods'             => array('PATCH', 'PUT', 'POST'),
                'callback'            => array($this, 'update_cpt_data'),
                'permission_callback' => array($this, 'permission_call'),

                'args' => array(

                    'title' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => array($this, 'validate_title'),
                    ),

                    'content' => array(
                        'required'                => true,
                        'sanitize_callback'       => 'sanitize_textarea_field',
                        'validate_callback'       => array($this, 'validate_content'),
                    ),

                    '_capacity' => array(
                        'required'          => true,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => array($this, 'validate_capacity'),
                    ),
                )
            )
        );

        register_rest_route(
            'myapi/v1',
            '/resource/(?P<id>\d+)',
            array(
                'methods'             => array('Delete'),
                'callback'            => array($this, 'delete_cpt_data'),
                'permission_callback' => array($this, 'permission_call'),
            )
        );

        register_rest_route('myapi/v1', '/resourcess/categorise/', array(
            'methods'             => 'GET',
            'callback'            => array($this, 'get_cpt_data_categorised'),
            'permission_callback' => '__return_true',
        ));
    }


    public function get_cpt_data_categorised(WP_REST_Request $request)
    {
        $search = sanitize_text_field(
            $request->get_param('search') ?? ''
        );

        $sort_by = sanitize_key(
            $request->get_param('sortBy') ?? 'a-z'
        );


        $args = array(
            'post_type'      => 'resource',
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        );

        $order = [
            'a-z' => [
                'orderby' => 'title',
                'order'   => 'ASC'
            ],
            'z-a' => [
                'orderby' => 'title',
                'order'   => 'DESC'
            ],
            'new' => [
                'orderby' => 'date',
                'order'   => 'DESC'
            ],
            'old' => [
                'orderby' => 'date',
                'order'   => 'ASC'
            ]
        ];

        if ($search !== "") {
            $args['s'] = esc_html($search);
        }

        if (isset($order[$sort_by])) {
            $args['orderby'] = $order[$sort_by]['orderby'];
            $args['order']   = $order[$sort_by]['order'];
        }

        $posts = get_posts($args);

        $data = array();

        foreach ($posts as $post) {
            $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'full');
            $capacity      = get_post_meta($post->ID, '_capacity', true);
            $data[] = array(
                'id'         => $post->ID,
                'title'      => $post->post_title,
                'slug'       => $post->post_name,
                'thumbnail'  => $thumbnail_url,
                '_capacity'  => $capacity,
                'content'    => $post->post_content
            );
        }
        return new WP_REST_Response($data, 200);
    }


    public function get_cpt_data()
    {
        $args = array(
            'post_type'      => 'resource',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        );

        $posts = get_posts($args);

        $data = array();

        foreach ($posts as $post) {
            $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'full');
            $capacity      = get_post_meta($post->ID, '_capacity', true);
            $data[] = array(
                'id'         => $post->ID,
                'title'      => $post->post_title,
                'slug'       => $post->post_name,
                'thumbnail'  => $thumbnail_url,
                '_capacity'  => $capacity,
                'content'    => $post->post_content
            );
        }
        return new WP_REST_Response($data, 200);
    }

    public function get_cpt_by_id(WP_REST_Request $request)
    {
        $id   = $request->get_param('id');

        $post = get_post($id);

        if ($post) {
            $data = array();

            $thumbnail_url = get_the_post_thumbnail_url($id, 'full');
            $capacity      = get_post_meta($post->ID, '_capacity', true);

            $data[] = array(
                'id'         => $post->ID,
                'title'      => $post->post_title,
                'slug'       => $post->post_name,
                'thumbnail'  => $thumbnail_url,
                '_capacity'  => $capacity,
                'content'    => $post->post_content
            );
            return new \WP_REST_Response($data, 200);
        } else {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => 'Invalid resource ID.',
                ),
                400
            );
        }
    }

    public function add_cpt_data(WP_REST_Request $request)
    {

        $title    = sanitize_text_field($request->get_param('title'));
        $content  = sanitize_textarea_field($request->get_param('content'));
        $capacity = absint($request->get_param('capacity'));


        $postarr = array(
            'post_title'   => $title,
            'post_type'    => 'resource',
            'post_status'  => 'publish',
            'post_content' => $content,
        );

        $post_id = wp_insert_post($postarr, true);


        if (is_wp_error($post_id)) {

            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => $post_id->get_error_message(),
                ),
                500
            );
        }

        update_post_meta(
            $post_id,
            '_capacity',
            $capacity
        );

        if (! isset($_FILES['file']) || empty($_FILES['file']['name'])) {
            return new WP_Error(
                'file_required',
                'Please upload a resource image.',
                array('status' => 400)
            );
        } else {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            $attachment_id = media_handle_upload(
                'file',
                $post_id
            );


            if (is_wp_error($attachment_id)) {

                return new WP_REST_Response(
                    array(
                        'success'     => false,
                        'resource_id' => $post_id,
                        'message'     => $attachment_id->get_error_message(),
                    ),
                    500
                );
            }


            set_post_thumbnail(
                $post_id,
                $attachment_id
            );
        }

        return new WP_REST_Response(
            array(
                'success'     => true,
                'resource_id' => $post_id,
                'image_id'    => $attachment_id ?? null,
                'message'     => 'Resource created successfully.',
            ),
            201
        );
    }

    public function update_cpt_data(WP_REST_Request $request)
    {

        $id = absint($request->get_param('id'));

        if (! $id) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => 'Invalid resource ID.',
                ),
                400
            );
        }

        $post = get_post($id);

        if (! $post || $post->post_type !== 'resource') {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => 'Resource not found.',
                ),
                404
            );
        }

        $data = array(
            'ID'          => $id,
            'post_type'  => 'resource',
            'post_status' => 'publish',
        );

        $has_updates = false;

        $title = $request->get_param('title');

        if (! empty($title)) {

            $data['post_title'] = sanitize_text_field($title);

            $has_updates = true;
        }


        $content = $request->get_param('content');

        if (! empty($content)) {

            $data['post_content'] = sanitize_textarea_field($content);

            $has_updates = true;
        }


        if ($has_updates) {

            $updated = wp_update_post($data, true);

            if (is_wp_error($updated)) {

                return new WP_REST_Response(
                    array(
                        'success' => false,
                        'message' => $updated->get_error_message(),
                    ),
                    500
                );
            }
        }


        $capacity = $request->get_param('_capacity');

        if ($capacity !== null) {

            update_post_meta(
                $id,
                '_capacity',
                absint($capacity)
            );
        }

        if (! empty($_FILES['file'])) {

            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            $current_attachment_id = get_post_thumbnail_id($id);

            if ($current_attachment_id) {
                wp_delete_attachment(
                    $current_attachment_id,
                    true
                );
            }

            $new_attachment_id = media_handle_upload(
                'file',
                $id
            );

            if (is_wp_error($new_attachment_id)) {

                return new WP_REST_Response(
                    array(
                        'success' => false,
                        'message' => $new_attachment_id->get_error_message(),
                    ),
                    500
                );
            }

            set_post_thumbnail(
                $id,
                $new_attachment_id
            );
        }

        return new WP_REST_Response(
            array(
                'success'     => true,
                'resource_id' => $id,
                'message'     => 'Resource updated successfully.',
            ),
            200
        );
    }

    public function delete_cpt_data(WP_REST_Request $request)
    {

        $id = absint($request->get_param('id'));

        $thumbnail_id = get_post_thumbnail_id($id);

        if ($thumbnail_id) {
            wp_delete_attachment($thumbnail_id, true);
        }

        $deleted = wp_delete_post($id, true);

        if (! $deleted) {
            return new WP_Error('delete_failed', 'Failed to delete resource.', ['status' => 500]);
        }

        return new WP_REST_Response([
            'success'     => true,
            'resource_id' => $id,
            'message'     => 'Resource and its thumbnail deleted successfully.',
        ], 200);
    }

    // Validations 

    public function validate_title($value)
    {
        if (empty($value)) {
            return new WP_Error(
                'invalid_title',
                'Title is required.',
                array('status' => 400)
            );
        }
    }

    public function validate_file($value)
    {
        if (empty($value)) {
            return new WP_Error(
                'file_required',
                'Please upload a resource image.',
                array('status' => 400)
            );
        }
    }

    public function validate_content($value)
    {

        if (empty($value)) {
            return new WP_Error(
                'invalid_content',
                'Content is required.',
                array('status' => 400)
            );
        }
    }

    public function validate_capacity($value)
    {

        if ($value < 4) {
            return new WP_Error(
                'invalid_capacity',
                'Capacity must be at least 4.',
                array('status' => 400)
            );
        }
    }

    public function permission_call()
    {
        // error_log(
        //     'User ID: ' . get_current_user_id()
        // );

        // error_log(
        //     'Can edit others: ' .
        //         (current_user_can('edit_others_posts') ? 'YES' : 'NO')
        // );
        return current_user_can('edit_others_posts');
    }
}

new Booking_CPT_Rest();
