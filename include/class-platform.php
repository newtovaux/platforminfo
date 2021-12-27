<?php

/**
 * Class for initializing the hooks and actions.
 *
 * @package Platform
 */

/* Quit */
defined( 'ABSPATH' ) || exit;

/**
 * Platform is a class
 * 
 * @package Platform
 * @author Newtovaux <newtovaux@gmail.com>
 */
final class Platform {

    /**
     * Psuedo constructor for static instance
     *
     * @return void
     */
    public static function instance() {
		new self();
	}

    /**
     * Constructor
     */
    public function __construct() {
        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', [__CLASS__, 'load_admin_styles' ] );
            add_action( 'admin_menu', [ __CLASS__, 'platform_options_page' ] );
        }
    }

    /**
     * Add the styles and JavaScript
     *
     * @return void
     */
    public static function load_admin_styles() {
        wp_enqueue_style('platform', plugins_url( 'platform', '_FILE_' ).'/admin/css/platform.css');
        wp_enqueue_script('platform', plugins_url( 'platform', '_FILE_' ).'/admin/js/platform.js', array(), 5.8, true);
    }

    /**
     * Add menu item
     *
     * @return void
     */
    public static function platform_options_page() {
        add_menu_page(
            'Platform - Shared Hosting Details', // $page_title
            'Platform', //$menu_title
            'manage_options', // $capability
            'platform', // $menu_slug
            [__CLASS__, 'platformwpplugin_options_page_html'],
            'dashicons-yes', // $icon_url
            5
        );
    }

    public static function platformwpplugin_options_page_html() {

        if (function_exists('ini_get_all')) {
            $php_ini_settings = ini_get_all();
        } else {
            $php_ini_settings = [];
        }
    
        $settings_to_display = [
            [
                'setting' => 'file_uploads',
                'description' => 'Whether or not to allow HTTP file uploads',
                'type' => 'bool',
            ],
            [
                'setting' => 'post_max_size',
                'description' => 'Sets max size of post data allowed. This setting also affects file upload.',
            ],
            [
                'setting' => 'upload_max_filesize',
                'description' => 'The maximum size of an uploaded file.',
            ],
            [
                'setting' => 'memory_limit',
                'description' => 'This sets the maximum amount of memory in bytes that a script is allowed to allocate.',
            ],
            [
                'setting' => 'disable_functions',
                'description' => 'This directive allows you to disable certain functions',
                'type' => 'array',
            ],
            [
                'setting' => 'max_execution_time',
                'description' => 'This sets the maximum time in seconds a script is allowed to run before it is terminated by the parser.',
            ],
            [
                'setting' => 'display_errors',
                'description' => 'This determines whether errors should be printed to the screen as part of the output or if they should be hidden from the user.',
                'type' => 'bool'
            ]
        ];
        ?>
        <div class="wrap">
          <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
          <h2>Site</h2>
          <table class="platform">
            <tr class="platform_striped">
                <td>URL</td><td><?php echo site_url(); ?></td>
            </tr>
            <tr class="platform_striped">
                <td>WP Home Path</td><td><?php echo get_home_path(); ?></td>
            </tr>
          </table>
          <h2>Environment</h2>
          <table class="wp-list-table widefat fixed striped table-view-list">
              <thead>
                  <tr>
                      <th class="manage-column">Environment variable</th>
                      <th class="manage-column">Value</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                      $sort = $_ENV;
                      ksort($sort);
                      foreach ($sort as $e => $v) {
                        printf(
                            '<tr><td>%s</td><td>%s</td></tr>', 
                            $e,
                            $v
                        );
                      }
                  ?>
              </tbody>
          </table>
          <h2>PHP</h2>
          <p>PHP version: <?php echo phpversion(); ?></p>
          <table class="wp-list-table widefat fixed striped table-view-list">
              <thead>
                  <tr>
                      <th class="manage-column" width=”20%”>Common Important php.ini Setting</th>
                      <th class="manage-column" width=”20%”>Value</th>
                      <th class="manage-column" width=”60%”>Description</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                    foreach ($settings_to_display as $setting) {
                        switch($php_ini_settings[$setting['setting']]["local_value"]) {
                            case "-1":
                                printf('<tr><td>%s</td><td>-1 (Unlimited by PHP)</td><td>%s</td></tr>', $setting['setting'], $setting['description']);
                                break;
                            case null:
                            case '':
                                printf('<tr><td>%s</td><td>(Not set in php.ini)</td><td>%s</td></tr>', $setting['setting'], $setting['description']);
                                break;
                            default:
                                if ($setting['type'] === 'bool') {
                                    printf(
                                        '<tr><td>%s</td><td>%s (%s)</td><td>%s</td></tr>', 
                                        $setting['setting'], 
                                        $php_ini_settings[$setting['setting']]["local_value"],
                                        $php_ini_settings[$setting['setting']]["local_value"] ? 'True' : 'False',
                                        $setting['description']
                                    );
                                } elseif ($setting['type'] === 'array') {
                                    printf('<tr><td>%s</td>', $setting['setting']);
                                    $types = explode(',', $php_ini_settings[$setting['setting']]["local_value"]);
                                    echo '<td>';
                                    foreach ($types as $type) {
                                        printf('%s<br />', $type);
                                    }
                                    echo '</td>';
                                    printf('<td>%s</td></tr>', $setting['description']);
                                } else {
                                    printf(
                                        '<tr><td>%s</td><td>%s</td><td>%s</td></tr>', 
                                        $setting['setting'], 
                                        $php_ini_settings[$setting['setting']]["local_value"], 
                                        $setting['description']
                                    );
                                }
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
            <button type="button" class="platform_collapsible">OPcache Details</button>
            <div class="platform_content">
          <p>
              <?php
                if (function_exists('opcache_get_configuration')) {
                    
                    $config = opcache_get_configuration();
                    echo '<h3>OPcache Configuration</h3>';
                    self::recursive_ulli($config);
                    
                    $status = opcache_get_status();
                    unset($status['scripts']);
                    //$status['scripts'] = null; // Don't list out all the scripts
                    echo '<h3>OPcache Status</h3>';
                    self::recursive_ulli($status);
                }
            ?>
          </p>
            </div>
          <?php } ?>
          <hr />
          <a href="/wp-admin/plugin-install.php">Add Plugins</a>
        </div>
        <?php
    }
    
    public static function recursive_ulli($entry) {
        if (is_array($entry)) {
            echo '<ul class="platform">';
            foreach ($entry as $key => $value) {
                if (is_array($value)) {
                    echo '<li>';
                    echo "<b>$key</b>: ";
                    self::recursive_ulli($value);
                    echo '</li>';
                } else {
                    echo '<li>';
                    echo "$key = ";
                    if (is_bool($value)) {
                        echo $value?'true':'false';
                    } else {
                        echo $value;
                    }
                    echo '</li>';
                }
            }
            echo '</ul>';
        } else {
            echo '<li>';
            echo $entry;
            echo '</li>';
        }
    }

}