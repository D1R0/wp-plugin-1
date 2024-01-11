<?php
add_action('rest_api_init', 'register_project_delete_api_endpoint');

function register_project_delete_api_endpoint()
{
    register_rest_route('ch4/v1', '/delete-project/(?P<series>[a-zA-Z0-9-]+)', array(
        'methods'  => 'DELETE',
        'callback' => 'delete_project_api',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        },
        'args' => array(
            'param' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_string($param);
                },
                'sanitize_callback' => 'sanitize_text_field',
            ),
        ),
    ));
}

function delete_project_api($data)
{
    $project_series = $data['series'];
    $result = delete_project($project_series);

    if ($result) {
        return new WP_REST_Response(array('success' => true), 200);
    } else {
        return new WP_Error('project_not_found', 'Project not found', array('status' => 404));
    }
}

// Function to delete a project
function delete_project($project_series)
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'ch4_projects'; // Adjust to your actual table name
    $result = $wpdb->delete(
        $table_name,
        array('series' => $project_series),
        array('%s')
    );
    return $result !== false;
}