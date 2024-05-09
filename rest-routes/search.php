<?php
namespace McmsCP\RestRoutes\Search;
use WP_REST_Request;
use WP_REST_Response;


add_action( 'rest_api_init', function () {
    register_rest_route('mcms-cp/v1', '/search/', [
        'methods' => 'GET',
        'callback' => '\McmsCP\RestHandlers\Search\simpleSearch',
        'permission_callback' => '__return_true',
    ]);

});