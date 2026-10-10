<?php

namespace Aj\Table;

class Booking_Db
{

    public static function create_table()
    {

        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $table_name = $wpdb->prefix . 'bookings';

        $sql = "CREATE TABLE $table_name (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				resource_id BIGINT(20) UNSIGNED,
				requester_name VARCHAR(255),
				requester_email VARCHAR(255),
				start_time DATETIME,
				end_time DATETIME,
				status ENUM('pending','confirmed','expired','cancelled'),
				created_at DATETIME,
				PRIMARY KEY  (id)
                ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
