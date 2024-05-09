<?php
/*
Plugin Name: Command Palette Plugin
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Adds a command palette to the admin bar
Version: 1.0.0
Author: mbouclas
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

require_once 'rest-handlers/search.php';
require_once 'rest-handlers/boot.php';
require_once 'rest-routes/search.php';
require_once 'rest-routes/boot.php';
require_once 'middleware/auth.php';
require_once 'includes/search.php';
require_once 'includes/settings-page.php';
require_once 'includes/menu-page.php';

register_activation_hook( __FILE__, 'cp_plugin_activate' );
register_deactivation_hook( __FILE__, 'cp_plugin_deactivate' );
add_action( 'admin_enqueue_scripts', 'enque_cp_assets' );
add_action('admin_footer', 'add_custom_elements_to_footer', 999);
add_action('admin_bar_menu', 'add_cp_toolbar_button', 999);
add_filter('rest_pre_dispatch', 'McmsCP\Middleware\Auth\isValidRequest', 10, 4);

add_action('plugins_loaded', function () {
    // check for mcms maybe
    $post_types = get_post_types();
});

function cp_plugin_activate() {
    // do something
}

function cp_plugin_deactivate() {
    // do something
}

function enque_cp_assets( $hook ) {

    wp_enqueue_style(
        'command-palette-plugin-main-css',
        plugins_url( '/assets/styles.css', __FILE__ ),
        [],
        '1.0.3'
    );

    wp_enqueue_script(
        'command-palette-plugin-main-js',
        plugins_url( '/assets/wp-admin-command-palette.umd.js', __FILE__ ),
        [],
        '1.0.4',
        [
            'in_footer' => true,
            'type' => 'module'
        ]
    );


}

function add_custom_elements_to_footer() {
    // Adding it this way to make sure it's added last on the dom so that it will be on top of every other element
    ?>

    <div class="p-4" id="command-palette-placeholder">
<!--        <command-palette></command-palette>-->

    </div>
    <script>

        document.addEventListener("DOMContentLoaded", function() {
            // Create a new command-palette element
            let commandPalette = document.createElement('command-palette');
            commandPalette.style.position = 'relative'; // or 'absolute' or 'fixed'
            commandPalette.style.zIndex = '9999'; // any high value
            // Append the command-palette element to the body
            document.body.appendChild(commandPalette);
        });
    </script>
    <?php
}


function add_cp_toolbar_button($wp_admin_bar) {
    $args = array(
        'id' => 'refresh_data_button', // The ID of your button
        'title' => '<cp-toggle-modal-button></cp-toggle-modal-button>', // The title of your button with custom HTML
        'meta' => array(

        )
    );
    $wp_admin_bar->add_node($args);
}
