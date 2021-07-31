<?php

/**
 * Plugin Name:       Fuel WordPress Plugin
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       WordPress Plugin
 * Version:           1.0.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            newtovaux
 * License:           MIT
 */

 /**
 * Activate the plugin.
 */
function fuelwpplugin_activate() { 
    // Trigger our function that registers the custom post type plugin.
    // pluginprefix_setup_post_type(); 
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules(); 
}

register_activation_hook( __FILE__, 'fuelwpplugin_activate' );

function fuelwpplugin_options_page_html() {
    ?>
    <div class="wrap">
      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      <p>Some paragraph.</p>
    </div>
    <?php
}

add_menu_page(
    'Fuel Plugin', // $page_title
    'Fuel Plugin', //$menu_title
    'manage_options', // $capability
    'fuelplugin', // $menu_slug
    'fuelwpplugin_options_page_html',
    'dashicons-admin-generic', // $icon_url
    null
);