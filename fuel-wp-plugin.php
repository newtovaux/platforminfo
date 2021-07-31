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
