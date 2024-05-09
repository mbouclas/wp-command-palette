<?php

namespace McmsCP\Includes\MenuPage;
add_action('admin_menu', '\McmsCP\Includes\MenuPage\addMenuPage');


function addMenuPage()
{
    add_submenu_page(
        'options-general.php',
        'Command Palette Settings',
        'Command Palette',
        'manage_options',
        'command-palette-admin',
        '\McmsCP\Includes\MenuPage\menuPageHtml'
    );

}

function menuPageHtml()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    $postTypes = get_post_types([], 'objects');
    $taxonomies = get_taxonomies([], 'objects');

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $selectedPostTypes = [];
        $selectedTaxonomies = [];
        // Loop through all post types
        foreach ($postTypes as $postType) {
            // If the checkbox for the post type was checked, add it to the array
            if (isset($_POST[$postType->name])) {
                $selectedPostTypes[] = sanitize_text_field($_POST[$postType->name]);
            }
        }

        foreach ($taxonomies as $taxonomy) {
            // If the checkbox for the post type was checked, add it to the array
            if (isset($_POST[$taxonomy->name])) {
                $selectedTaxonomies[] = sanitize_text_field($_POST[$taxonomy->name]);
            }
        }

        // Save the array in the WordPress database
        update_option('my_plugin_selected_post_types', $selectedPostTypes);
        update_option('my_plugin_selected_taxonomies', $selectedTaxonomies);
    }

    // Retrieve the array from the database to check the checkboxes
    $selectedPostTypes = get_option('my_plugin_selected_post_types', []);
    $selectedTaxonomies = get_option('my_plugin_selected_taxonomies', []);
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="test">Test:</label></th>
                    <td>
                        <?php
                        foreach ($postTypes as $postType) {
                            ?>
                            <input type="checkbox" id="<?php echo $postType->name; ?>" name="<?php echo $postType->name; ?>"
                                   value="<?php echo $postType->name; ?>" <?php echo in_array($postType->name, $selectedPostTypes) ? 'checked' : ''; ?>>
                            <label for="<?php echo $postType->name; ?>"><?php echo $postType->label; ?></label><br>
                            <?php
                        }
                        ?>

                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="test">Test:</label></th>
                    <td>
                        <?php
                        foreach ($taxonomies as $taxonomy) {
                            ?>
                            <input type="checkbox" id="<?php echo $taxonomy->name; ?>" name="<?php echo $taxonomy->name; ?>"
                                   value="<?php echo $taxonomy->name; ?>" <?php echo in_array($taxonomy->name, $selectedTaxonomies) ? 'checked' : ''; ?>>
                            <label for="<?php echo $taxonomy->name; ?>"><?php echo $taxonomy->label; ?></label><br>
                            <?php
                        }
                        ?>

                    </td>
            </table>
            <?php submit_button('Save'); ?>
        </form>
    </div>
    <?php
}