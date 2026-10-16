<?php

namespace Aj\Booking\Frontend;

class Resource_List
{
    public function __construct()
    {
        add_shortcode('booking_resources', array($this, 'display_resources'));
        add_shortcode('demowork', array($this, 'add_shortcode_fn'));
    }

    public function display_resources()
    {
        return '<h2>Our Booking Resources</h2>';
    }

    public function add_shortcode_fn()
    {
        // Enqueue script only when the shortcode is actually rendered on a page
        wp_enqueue_script(
            'booking-public-script',
            plugin_dir_url(dirname(__DIR__)) . 'booking-project/public/js/booking-public.js',
            array('jQuery'),
            '1.0.0',
            true
        );

        $file_path = plugin_dir_path(__FILE__) . './frontend/cards.php';
        if (file_exists($file_path)) {
            ob_start();
            include $file_path;
            return ob_get_clean();
        }
        return '<p>Template not found.</p>';
    }
}