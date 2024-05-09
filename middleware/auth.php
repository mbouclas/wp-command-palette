<?php
namespace McmsCP\Middleware\Auth;
use WP_REST_Request;
use WP_Error;
use WP_REST_Server;

function isValidRequest( $result, WP_REST_Server $server, WP_REST_Request $request) {
    $referer_host = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) : null;
    if (!$referer_host) {
        return null;
    }

    return new WP_Error('rest_forbidden', 'Invalid API Key', array('status' => 403));
}