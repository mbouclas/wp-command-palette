<?php
namespace McmsCP\RestHandlers\Search;
use McmsCP\Includes\Search\Search;
use WP_REST_Request;
use WP_REST_Response;


function simpleSearch(WP_REST_Request $request) {
    $searchTerm = $request->get_param('q');
    $searchService = new Search();
    $posts = $searchService->searchPosts($searchTerm);
    $taxonomies = $searchService->searchTaxonomies($searchTerm);
    $byId = $searchService->searchById($searchTerm);
    return new WP_REST_Response([
        'posts' => $posts,
        'taxonomies' => $taxonomies,
        'byId' => $byId,
    ]);
}