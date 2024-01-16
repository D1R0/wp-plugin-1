<?php
/*
Plugin Name: CH4.ro
Description: Eltand, your online partner.
Version: 1.0
Author: Eltand
Author URI: https://www.eltand.com/
*/

if (!defined('WPINC')) {
    die;
}

add_filter('plugin_row_meta', 'ch4_add_author_link', 10, 2);

function ch4_plugin_enqueue_styles_scripts()
{
    wp_enqueue_style('ch4-admin-style', plugins_url('assets/css/style.css', __FILE__));
    wp_enqueue_script('ch4-admin-script', plugins_url('assets/js/script.js', __FILE__), array('jquery'), null, true);

    wp_enqueue_style('datatables', 'https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('datatables', 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js', array('jquery'), null, true);
}
register_activation_hook(__FILE__, 'ch4_plugin_activate');

function ch4_plugin_admin_menu()
{
    add_menu_page('Proiecte', 'CH4', 'manage_options', 'ch4', 'ch4_admin');
}

add_action('admin_menu', 'ch4_plugin_admin_menu');
add_action('admin_enqueue_scripts', 'ch4_plugin_enqueue_styles_scripts');
add_shortcode('project_search_form', 'ch4_plugin_project_search_form');
add_action('rest_api_init', 'register_project_delete_api_endpoint');

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
require_once plugin_dir_path(__FILE__) . 'project-functions.php';
require_once plugin_dir_path(__FILE__) . 'edit-project-form.php';
require_once plugin_dir_path(__FILE__) . 'components.php';
require_once plugin_dir_path(__FILE__) . 'rest.php';