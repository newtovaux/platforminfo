<?php
/**
 * Plugin Name:       PlatformInfo
 * Plugin URI:        https://en-gb.wordpress.org/plugins/platforminfo/
 * Description:       Gives useful infomation for sites on shared-hosting.
 * Version:           1.1.0
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

/* Hooks */
add_action(
	'plugins_loaded',
	array(
		'Platforminfo',
		'instance',
	)
);

require plugin_dir_path( __FILE__ ) . 'include/class-platforminfo.php';
