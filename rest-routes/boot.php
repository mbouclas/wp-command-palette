<?php
namespace McmsCP\RestRoutes\Boot;

add_action( 'rest_api_init', function () {
    register_rest_route('mcms-cp/v1', '/boot/', [
        'methods' => 'GET',
        'callback' => '\McmsCP\RestHandlers\Boot\init',
        'permission_callback' => '__return_true',
    ]);

});