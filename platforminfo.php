<?php
/**
 * Plugin Name:       Platform Info
 * Description:       Gives useful infomation for sites on shared-hosting.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            newtovaux
 * License:           GPLv2
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

/* Register autoload */
spl_autoload_register( 'platforminfo_autoload' );

/**
 * Autoload the classes
 *
 * @param string $class the class name.
 *
 * @return void
 */
function platforminfo_autoload( $class ) {
	if ( in_array( $class, array( 'Platforminfo' ), true ) ) {
		include_once sprintf(
			'%s/include/class-%s.php',
			dirname( __FILE__ ),
			strtolower( str_replace( '_', '-', $class ) )
		);
	}
}
