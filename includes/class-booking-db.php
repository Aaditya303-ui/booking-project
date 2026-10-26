<?php

namespace Aj\Table;

class Booking_Db
{
    public static function create_table()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $table_name  = $wpdb->prefix . 'bookings';
        $table_name1 = $wpdb->prefix . 'blackout';
        $table_name2 = $wpdb->prefix . 'business_hours';

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            resource_id BIGINT(20) UNSIGNED,
            requester_name VARCHAR(255),
            requester_email VARCHAR(255),
            start_time TIME,
            end_time TIME,
            status ENUM('pending','confirmed','expired','cancelled'),
            created_at DATETIME,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql1 = "CREATE TABLE $table_name1 (
            blackout_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            blackout_date DATETIME,
            reason VARCHAR(255),
            PRIMARY KEY  (blackout_id)
        ) $charset_collate;";

        $sql2 = "CREATE TABLE $table_name2 (
            id INT(11) NOT NULL AUTO_INCREMENT,
            day_of_week VARCHAR(10) NOT NULL,
            open_time TIME DEFAULT '09:00:00',
            close_time TIME DEFAULT '17:00:00',
            is_closed TINYINT(1) DEFAULT 0,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql);
        dbDelta($sql1);
        dbDelta($sql2);
    }

    public static function create_table_blackout()
    {
        self::create_table();
    }
}
