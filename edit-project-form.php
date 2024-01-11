<?php

function ch4_plugin_activate()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'ch4_projects';
    $charset_collate = $wpdb->get_charset_collate();
    if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
        series VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        status VARCHAR(255),
        details LONGTEXT,
        PRIMARY KEY (series)
    ) $charset_collate;";
    }

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}