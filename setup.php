<?php

function ch4_plugin_activate()
{
    (new ch4_Activater)->updateDatabase();
}
class ch4_Activater
{

    public function updateDatabase()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ch4_projects';
        $charset_collate = $wpdb->get_charset_collate();

        if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
            $sql = "CREATE TABLE $table_name (
                series VARCHAR(191) NOT NULL,
                name VARCHAR(191) NOT NULL,
                status VARCHAR(191),
                details LONGTEXT,
                PRIMARY KEY (series)
            ) $charset_collate;";
            $result = $wpdb->get_results($sql);

            // // Execute the SQL query
            // require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            // dbDelta($sql);
        }
    }
}
