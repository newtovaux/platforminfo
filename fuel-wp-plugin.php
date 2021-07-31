<?php

/**
 * Plugin Name:       Fuel
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       A WordPress plugins to give useful info for shared-hosting 
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            newtovaux
 * License:           MIT
 */


 /**
 * Activate the plugin.
 */
// function fuelwpplugin_activate() { 
//     // Trigger our function that registers the custom post type plugin.
//     // pluginprefix_setup_post_type(); 
//     // Clear the permalinks after the post type has been registered.
//     flush_rewrite_rules(); 
// }

// register_activation_hook( __FILE__, 'fuelwpplugin_activate' );

function fuelwpplugin_options_page_html() {

    if (function_exists('ini_get_all')) {
        $php_ini_settings = ini_get_all();
    } else {
        $php_ini_settings = [];
    }

    $settings_to_display = [
        'file_uploads',
        'post_max_size',
        'upload_max_filesize',
        'memory_limit',
        'disable_functions',
        'max_execution_time',
        'display_errors'
    ];
    ?>
    <div class="wrap">
      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      <h2>Site</h2>
      <table class="fuel">
        <tr class="fuel_striped">
            <td>URL</td><td><?php echo site_url(); ?></td>
        </tr>
        <tr class="fuel_striped">
            <td>WP Home Path</td><td><?php echo get_home_path(); ?></td>
        </tr>
      </table>
      <h2>PHP</h2>
      <p>PHP version: <?php echo phpversion(); ?></p>
      <table class="wp-list-table widefat fixed striped table-view-list">
          <thead>
              <tr>
                  <th class="manage-column">php.ini Setting</th>
                  <th class="manage-column">Value</th>
              </tr>
          </thead>
          <tbody>
              <?php
                foreach ($settings_to_display as $setting) {
                    switch($php_ini_settings["$setting"]["local_value"]) {
                        case "-1":
                            printf('<tr><td>%s</td><td>-1 (Unlimited by PHP)</td></tr>', $setting);
                            break;
                        case null:
                        case '':
                            printf('<tr><td>%s</td><td>(Not set in php.ini)</td></tr>', $setting);
                            break;
                        default:
                            printf('<tr><td>%s</td><td>%s</td></tr>', $setting, $php_ini_settings["$setting"]["local_value"]);
                            break;
                    }
                }
              ?>
          </tbody>
      </table>
      <h2>PHP Extensions</h2>
      <p>List of all PHP modules compiled and loaded:</p>
      <div style="border: 1px solid #c3c4c7; background-color: #ffffff; padding: 8px 10px">
      <ul>
          <?php
            foreach (get_loaded_extensions() as $ext) {
                printf('<li style="display: inline-block; padding-right: 10px;">%s</li>', $ext);
            }
          ?>
      </ul>
      </div>
      <h2>PHP OPcache</h2>
      <p>OPcache improves PHP performance by storing precompiled script bytecode in shared memory, thereby removing the need for PHP to load and parse scripts on each request.</p>
      <?php if (!extension_loaded('Zend OPcache')) { ?>
        <div class="notice notice-error"><p>You do not have the Zend OPcache extension loaded.</p></div>
        <?php
            if ((function_exists('dl') == false) || (dl('opcache.so') == false)) { ?>
            <div class="notice notice-error"><p>Unable to load Zend OPcache extension.</p></div>
        <?php } ?>
        <p>OPcache not available.</p>
      <?php } else { ?>
      <p>
          <?php
            if (function_exists('opcache_get_configuration')) {
                
                $config = opcache_get_configuration();
                echo '<h3>OPcache Configuration</h3>';
                echo '<div class="fuel_status_box">';
                recursive_ulli($config);
                echo '</div>';
                
                $status = opcache_get_status();
                unset($status['scripts']);
                //$status['scripts'] = null; // Don't list out all the scripts
                echo '<h3>OPcache Status</h3>';
                echo '<div class="fuel_status_box">';
                recursive_ulli($status);
                echo '</div>';
            }
        ?>
      </p>
      <?php } ?>
      <hr />
      <a href="/wp-admin/plugin-install.php">Plugin Install</a>
    </div>
    <?php
}

function load_admin_styles() {
    wp_enqueue_style('fuel', plugins_url( 'fuel-wp-plugin', '_FILE_' ).'/public/css/fuel.css');
}

if ( is_admin() ) {
    add_action( 'admin_enqueue_scripts', 'load_admin_styles' );
    add_action( 'admin_menu', 'fuel_options_page' );
}

function fuel_options_page() {
    add_menu_page(
        'Fuel - Shared Hosting Details', // $page_title
        'Fuel', //$menu_title
        'manage_options', // $capability
        'fuel', // $menu_slug
        'fuelwpplugin_options_page_html',
        'dashicons-admin-generic', // $icon_url
        5
    );
}

function recursive_ulli($entry)
{
    if (is_array($entry)) {
        if ($entry === []) {
            echo '<li>(none)</li>';
        } else {
            foreach ($entry as $key => $value) {
                if (is_array($value))
                {
                    echo "<ul style=\"list-style=\"inside\"\"><b>$key</b>: ";
                    recursive_ulli($value);
                    echo "</ul>";
                } else {
                    if (is_bool($value)) {
                        printf("<li>$key = %s</li>", $value?'true':'false');
                    } elseif ($value === '') {
                        printf("<li>$key = (none)</li>");
                    } else {
                        printf("<li>$key = $value</li>");
                    }
                }
            }
        }
    }
}