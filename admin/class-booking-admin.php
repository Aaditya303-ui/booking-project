<?php

// namespace Aj\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://http:example.com
 * @since      1.0.0
 *
 * @package    Booking
 * @subpackage Booking/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Booking
 * @subpackage Booking/admin
 * @author     aj <test@gmail.com>
 */
class Booking_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Booking_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Booking_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style(
			$this->plugin_name . '-admin-style',
			plugin_dir_url(__FILE__) . 'css/booking-admin.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		$handle = $this->plugin_name;

		wp_enqueue_script(
			$handle,
			plugin_dir_url(__FILE__) . 'js/booking-admin.js',
			array('jquery'),
			$this->version,
			false
		);

		wp_enqueue_script(
			'sweetalert2',
			'https://cdn.jsdelivr.net/npm/sweetalert2@11',
			array(),
			'11',
			true
		);

		wp_enqueue_script(
			"$handle-request-booking",
			plugin_dir_url(__FILE__) . 'js/request-admin.js',
			array('jquery'),
			$this->version,
			false
		);

		wp_enqueue_script(
			"$handle-blackout-booking",
			plugin_dir_url(__FILE__) . 'js/blackout-admin.js',
			array('jquery'),
			$this->version,
			false
		);

		wp_enqueue_script(
			"$handle-buisness-booking",
			plugin_dir_url(__FILE__) . 'js/buiseness-admin.js',
			array('jquery'),
			$this->version,
			false
		);

		wp_localize_script($handle, 'bookingApiSettings', array(
			'nonce' => wp_create_nonce('wp_rest')
		));
	}

	public static function add_booking_menu()
	{
		add_menu_page(
			'Resource Admin Side',
			'Resource page',
			'manage_options',
			'resource-admin-page',
			array(__CLASS__, 'resource_admin_page_callback')
		);

		add_submenu_page(
			'resource-admin-page',
			'add resource',
			'add resource',
			'manage_options',
			'add-resource',
			array(__CLASS__, 'add_resource_call')
		);

		add_submenu_page(
			'resource-admin-page',
			'display requests',
			'display request',
			'manage_options',
			'display-request',
			array(__CLASS__, 'display_request_call')
		);

		add_submenu_page(
			'resource-admin-page',
			'blackout dates',
			'blackout dates',
			'manage_options',
			'display-booking-settings',
			array(__CLASS__, 'display_booking_settings')
		);

		add_submenu_page(
			'resource-admin-page',
			'Buisness Hours',
			'Buisness Hours',
			'manage_options',
			'display-buisness-settings',
			array(__CLASS__, 'display_buisness_settings')
		);
	}



	public static function resource_admin_page_callback()
	{
		require_once plugin_dir_path(__FILE__) . '/partials/heading.php';
	}

	public static function add_resource_call()
	{
		require_once plugin_dir_path(__FILE__) . '/partials/add_resource.php';
	}

	public static function display_request_call()
	{
		require_once plugin_dir_path(__FILE__) . '/partials/display_request.php';
	}

	public static function display_booking_settings()
	{
		require_once plugin_dir_path(__FILE__) . '/partials/display_settings.php';
	}

	public static function display_buisness_settings()
	{
		require_once plugin_dir_path(__FILE__) . '/partials/display_buisness.php';
	}
}

add_action('admin_menu', [Booking_Admin::class, 'add_booking_menu']);
