<?php

namespace Aj\Post;

class Booking_Post
{
    public static function create_post()
    {
        $args = array(
            'labels' => array(
                'name'          => 'Resources',
                'singular_name' => 'Resource',
                'menu_name'     => 'Resources',
                'add_new'       => 'Add New Resource',
                'add_new_item'  => 'Add New Resource',
                'new_item'      => 'New Resource',
                'edit_item'     => 'Edit Resource',
                'view_item'     => 'View Resources',
                'all_items'     => 'All Resources',
            ),
            'rewrite' => array( 'slug' => 'resource'),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'show_in_menu' => true,
            'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt'),
        );

        register_post_type('resource', $args);

        register_post_meta('resource','booking_capacity',array(
            'type'              => 'integer',
            'single'            => true,
            'default'           => 1,
            'show_in_rest'      => true,
            'sanitize_callback' => 'absint'
        ));
    }
}
