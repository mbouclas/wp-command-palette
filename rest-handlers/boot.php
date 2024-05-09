<?php
namespace McmsCP\RestHandlers\Boot;
use McmsCP\Includes\Search\Search;
use WP_REST_Request;
use WP_REST_Response;


function init(WP_REST_Request $request) {
    $selectedPostTypes = get_option('my_plugin_selected_post_types', []);
    $selectedTaxonomies = get_option('my_plugin_selected_taxonomies', []);
    $allowedPostTypes = [];
    $allowedTaxonomies = [];
    $postTypes = get_post_types([], 'objects');
    $taxonomies = get_taxonomies([], 'objects');

    foreach ($selectedPostTypes as $key => $postType) {
        if (!post_type_exists($postType)) {
            unset($selectedPostTypes[$key]);
        }

        if (!isset($postTypes[$postType])) {
            continue;
        }


        $allowedPostTypes[] = [
            'name' => $postTypes[$postType]->label,
            'slug' => $postType,
        ];
    }

    foreach ($taxonomies as $taxonomy) {
        if (!in_array($taxonomy->name, $selectedTaxonomies)) {
            continue;
        }

        $allowedTaxonomies[] = [
            'name' => $taxonomy->label,
            'slug' => $taxonomy->name,
        ];
    }


    return new WP_REST_Response([
        'allowedPostTypes' => $allowedPostTypes,
        'allowedTaxonomies' => $allowedTaxonomies,
    ]);
}