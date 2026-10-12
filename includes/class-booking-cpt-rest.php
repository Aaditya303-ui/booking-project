<?php
namespace Aj\Booking;

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
            '/resource',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_cpt_data'),
                'permission_callback' => '__return_true'
            )
        );
    }

    public function get_cpt_data()
    {
        $args = array(
            'post_type'   => 'resource',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );

        $posts = get_posts($args);

        $data = array();

        foreach($posts as $post) {
            $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'full');
            $data[] = array(
                'id'        => $post->ID,
                'title'     => $post->post_title,
                'slug'      => $post->post_name,
                'thumbnail' => $thumbnail_url,
                'content'   => add_filter('the_content',$post->post_content)
            );
        }
        return new \WP_REST_Response($data, 200);   
    }
}


new Booking_CPT_Rest();