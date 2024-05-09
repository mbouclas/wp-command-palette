<?php
namespace McmsCP\Includes\Search;

class Search {
    public $posts;
    public $postTypes;
    public $taxonomies;

    public $allowedPostTypes = [];

    public function __construct() {
        $this->posts = [];
        $postTypes = get_post_types([], 'objects');
        foreach ($postTypes as $postType) {
            $this->postTypes[] = $postType;
        }

        $taxonomies = get_taxonomies([], 'objects');
        foreach ($taxonomies as $taxonomy) {
            $this->taxonomies[] = $taxonomy;
        }

        // get allowed post types from the settings
    }

    public function searchTaxonomies($searchTerm)
    {
        $selectedTaxonomies = get_option('my_plugin_selected_taxonomies', []);
        $results = [];
        // foreach taxonomy run a query to get all terms with the search term
        foreach ($selectedTaxonomies as $taxonomy) {
            $args = [
                'taxonomy' => $taxonomy,
                'name__like' => $searchTerm,
                'hide_empty' => false,
            ];
            error_log($taxonomy);
            $terms = get_terms($args);

            if (empty($terms)) {
                continue;
            }

            $results[$taxonomy] = $terms;
        }

        return $results;
    }
    public function searchPosts($searchTerm)
    {
        $selectedPostTypes = get_option('my_plugin_selected_post_types', []);

        // foreach post type run a query to get all posts with the search term
        foreach ($this->postTypes as $postType) {
            if (!in_array($postType->name, $selectedPostTypes)) {
                continue;
            }

            // Add filter to modify the WHERE clause of the query
            add_filter('posts_where', [$this, 'title_like'], 10, 2);



            $args = [
                'post_type' => $postType->name,
                's' => $searchTerm,
                'posts_per_page' => 10,
            ];
            $query = new \WP_Query($args);
//            error_log($query->request);
            // Remove the filter after running the query
            remove_filter('posts_where', [$this, 'title_like'], 10);

            $res = $query->posts;
            if (count($res) === 0) {
                continue;
            }

            $this->posts[$postType->name] = $res;
        }

        return $this->posts;
    }

    function title_like($where, &$wp_query) {
        global $wpdb;

        if ($search_term = $wp_query->get('s')) {
            /* using $wpdb->prepare() to ensure the search term is safely included in the query */
            $where .= ' AND ' . $wpdb->posts . '.post_title LIKE ' . $wpdb->prepare('%s', '%' . $wpdb->esc_like($search_term) . '%');
        }
        return $where;
    }


    public function searchById($id)
    {
        // Search in posts
        $args = [
            'p' => $id, // Search by post ID
        ];
        $query = new \WP_Query($args);
        $posts = $query->posts;

        // Search in terms
        $args = [
            'include' => [$id], // Search by term ID
            'hide_empty' => false,
        ];
        $terms = get_terms($args);

        // Search in post meta
        $args = [
            'meta_query' => [
                [
                    'key' => 'id', // Replace with your meta key
                    'value' => $id,
                    'compare' => 'LIKE'
                ]
            ]
        ];
        $query = new \WP_Query($args);
        $postMeta = $query->posts;

        // Search in product attributes
        $args = [
            'meta_query' => [
                [
                    'key' => '_product_attributes', // This is the meta key for product attributes in WooCommerce
                    'value' => $id,
                    'compare' => 'LIKE'
                ]
            ]
        ];
        $query = new \WP_Query($args);
        $productAttributes = $query->posts;

        return [
            'posts' => $posts,
            'terms' => $terms,
            'postMeta' => $postMeta,
            'productAttributes' => $productAttributes,
        ];
    }
}

