<?php

class Rest
{
    public static function register()
    {
        register_rest_route('ch4/v1', '/delete-project/(?P<series>[a-zA-Z0-9-]+)', array(
            'methods'  => 'DELETE',
            'callback' => [new Rest, 'delete_project_api'],
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
        register_rest_route('ch4/v1', '/ping', array(
            'methods'  => 'get',
            'callback' => [new Rest, 'ping'],
        ));
    }


    function delete_project_api($data)
    {
        $project_series = $data['series'];
        $projectsService = new ProjectService();
        $result = $projectsService->delete_project($project_series);
        if ($result) {
            return new WP_REST_Response(array('success' => true), 200);
        } else {
            return new WP_Error('project_not_found', 'Project not found', array('status' => 404));
        }
    }

    function ping()
    {
        return "pong";
    }
}