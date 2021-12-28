<?php

/**
 * Plugin Name:       Platform
 * Description:       This WordPress plugin gives useful infomation for sites on shared-hosting.
 * Version:           1.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            newtovaux
 * License:           GPLv2
 */

/* Quit */
defined( 'ABSPATH' ) || exit;

/* Hooks */
add_action(
	'plugins_loaded',
	array(
		'Platform',
		'instance',
	)
);

/* Register autoload */
spl_autoload_register( 'platform_autoload' );

/**
 * Autoload the classes
 *
 * @param string $class the class name.
 */
function platform_autoload( $class ) {
	if ( in_array( $class, array( 'Platform' ), true ) ) {
		require_once sprintf(
			'%s/include/class-%s.php',
			dirname( __FILE__ ),
			strtolower( str_replace( '_', '-', $class ) )
		);
	}
}
