<?php
/**
 * Plugin Name:       PlatformInfo
 * Plugin URI:        https://en-gb.wordpress.org/plugins/platforminfo/
 * Description:       PlatformInfo gives useful information for sites on shared hosting.
 * Version:           1.1.11
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            newtovaux
 * Author URI:        https://github.com/newtovaux/
 * License:           GPLv2
 * Text Domain:       platforminfo
 *
 * PHP version 7.4
 *
 * @category Plugin
 * @package  Platforminfo
 * @author   newtovaux <newtovaux@gmail.com>
 * @license  GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://github.com/newtovaux/platforminfo
 */

/* Quit */
defined( 'ABSPATH' ) || exit;

/* Define user constants */
if ( ! defined( 'PLATFORMINFO_BASE' ) ) {
	define( 'PLATFORMINFO_BASE', plugin_basename( __FILE__ ) );
}

/**
 * Ignore.
 *
 * @psalm-suppress UnresolvableInclude
 */
require plugin_dir_path( __FILE__ ) . 'include/class-platforminfo.php';

/* Hooks */
add_action(
	'plugins_loaded',
	'Platforminfo::instance'
);
