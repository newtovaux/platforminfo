<?php
/**
 * Class for initializing the hooks and actions.
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

/**
 * Platforminfo is a class
 *
 * @category Plugin
 * @package  Platforminfo
 * @author   Newtovaux <newtovaux@gmail.com>
 * @license  GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://github.com/newtovaux/platforminfo
 */
final class Platforminfo {

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
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'platforminfo_load_admin_styles' ) );
			add_action( 'admin_menu', array( __CLASS__, 'platforminfo_options_page' ) );
		}
	}

	/**
	 * Add the styles and JavaScript
	 *
	 * @return void
	 */
	public static function platforminfo_load_admin_styles() {
		wp_enqueue_style( 'platforminfo', plugins_url( 'platforminfo', '_FILE_' ) . '/admin/css/platforminfo.css', false );
		wp_enqueue_script( 'platforminfo', plugins_url( 'platforminfo', '_FILE_' ) . '/admin/js/platforminfo.js', array(), 5.8, true );
	}

	/**
	 * Add menu item
	 *
	 * @return void
	 */
	public static function platforminfo_options_page() {
		add_menu_page(
			'Platform Info - Shared Hosting Details', // $page_title
			'Platform', // $menu_title
			'manage_options', // $capability
			'platforminfo', // $menu_slug
			array( __CLASS__, 'platforminfo_wpplugin_options_page_html' ),
			'dashicons-yes', // $icon_url
			5
		);
	}

	/**
	 * Plugin display page
	 *
	 * @return void
	 */
	public static function platforminfo_wpplugin_options_page_html() {

		if ( function_exists( 'ini_get_all' ) ) {
			$php_ini_settings = ini_get_all();
		} else {
			$php_ini_settings = array();
		}

		$settings_to_display = array(
			array(
				'setting' => 'file_uploads',
				'description' => 'Whether or not to allow HTTP file uploads',
				'type' => 'bool',
			),
			array(
				'setting' => 'post_max_size',
				'description' => 'Sets max size of post data allowed. This setting also affects file upload.',
			),
			array(
				'setting' => 'upload_max_filesize',
				'description' => 'The maximum size of an uploaded file.',
			),
			array(
				'setting' => 'memory_limit',
				'description' => 'This sets the maximum amount of memory in bytes that a script is allowed to allocate.',
			),
			array(
				'setting' => 'disable_functions',
				'description' => 'This directive allows you to disable certain functions',
				'type' => 'array',
			),
			array(
				'setting' => 'max_execution_time',
				'description' => 'This sets the maximum time in seconds a script is allowed to run before it is terminated by the parser.',
			),
			array(
				'setting' => 'display_errors',
				'description' => 'This determines whether errors should be printed to the screen as part of the output or if they should be hidden from the user.',
				'type' => 'bool',
			),
		);
		?>
		<div class="wrap">
		  <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		  <h2>Site</h2>
		  <table class="platforminfo">
			<tr class="platforminfo_striped">
				<td>URL</td><td><?php echo esc_html( site_url() ); ?></td>
			</tr>
			<tr class="platforminfo_striped">
				<td>WP Home Path</td><td><?php echo esc_html( get_home_path() ); ?></td>
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
					  ksort( $sort );
					foreach ( $sort as $e => $v ) {
						printf(
							'<tr><td>%s</td><td>%s</td></tr>',
							esc_html( $e ),
							esc_html( $v )
						);
					}
					?>
			  </tbody>
		  </table>
		  <h2>PHP</h2>
		  <p>PHP version: <?php echo esc_html( phpversion() ); ?></p>
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
					foreach ( $settings_to_display as $setting ) {
						switch ( $php_ini_settings[ $setting['setting'] ]['local_value'] ) {
							case '-1':
								printf( '<tr><td>%s</td><td>-1 (Unlimited by PHP)</td><td>%s</td></tr>', esc_html( $setting['setting'] ), esc_html( $setting['description'] ) );
								break;
							case null:
							case '':
								printf( '<tr><td>%s</td><td>(Not set in php.ini)</td><td>%s</td></tr>', esc_html( $setting['setting'] ), esc_html( $setting['description'] ) );
								break;
							default:
								if ( 'bool' === $setting['type'] ) {
									printf(
										'<tr><td>%s</td><td>%s (%s)</td><td>%s</td></tr>',
										esc_html( $setting['setting'] ),
										esc_html( $php_ini_settings[ $setting['setting'] ]['local_value'] ),
										$php_ini_settings[ $setting['setting'] ]['local_value'] ? 'True' : 'False',
										esc_html( $setting['description'] )
									);
								} elseif ( 'array' === $setting['type'] ) {
									printf( '<tr><td>%s</td>', esc_html( $setting['setting'] ) );
									$types = explode( ',', $php_ini_settings[ $setting['setting'] ]['local_value'] );
									echo '<td>';
									foreach ( $types as $type ) {
										printf( '%s<br />', esc_html( $type ) );
									}
									echo '</td>';
									printf( '<td>%s</td></tr>', esc_html( $setting['description'] ) );
								} else {
									printf(
										'<tr><td>%s</td><td>%s</td><td>%s</td></tr>',
										esc_html( $setting['setting'] ),
										esc_html( $php_ini_settings[ $setting['setting'] ]['local_value'] ),
										esc_html( $setting['description'] )
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
				foreach ( get_loaded_extensions() as $ext ) {
					printf( '<li style="display: inline-block; padding-right: 10px;">%s</li>', esc_html( $ext ) );
				}
				?>
		  </ul>
		  </div>
		  <h2>PHP OPcache</h2>
		  <p>OPcache improves PHP performance by storing precompiled script bytecode in shared memory, thereby removing the need for PHP to load and parse scripts on each request.</p>
		  <?php if ( ! extension_loaded( 'Zend OPcache' ) ) { ?>
			<div class="notice notice-error"><p>You do not have the Zend OPcache extension loaded.</p></div>
				<?php
				if ( ( function_exists( 'dl' ) == false ) || ( dl( 'opcache.so' ) == false ) ) {
					?>
				<div class="notice notice-error"><p>Unable to load Zend OPcache extension.</p></div>
			<?php } ?>
			<p>OPcache not available.</p>
		  <?php } else { ?>
			<button type="button" class="platforminfo_collapsible">OPcache Details</button>
			<div class="platforminfo_content">
		  <p>
			  <?php
				if ( function_exists( 'opcache_get_configuration' ) ) {

					$config = opcache_get_configuration();
					echo '<h3>OPcache Configuration</h3>';
					self::recursive_ulli( $config );

					$status = opcache_get_status();
					unset( $status['scripts'] );
					// $status['scripts'] = null; // Don't list out all the scripts
					echo '<h3>OPcache Status</h3>';
					self::recursive_ulli( $status );
				}
				?>
		  </p>
			</div>
		  <?php } ?>
		  <hr />
		</div>
		<?php
	}

	/**
	 * Recursive function to display nested details
	 *
	 * @param mixed $entry Nested entry.
	 * @return void
	 */
	public static function recursive_ulli( $entry ) {
		if ( is_array( $entry ) ) {
			echo '<ul class="platforminfo">';
			foreach ( $entry as $key => $value ) {
				if ( is_array( $value ) ) {
					echo '<li>';
					printf( '<b>%s</b>: ', esc_html( $key ) );
					self::recursive_ulli( $value );
					echo '</li>';
				} else {
					echo '<li>';
					printf( '%s = ', esc_html( $key ) );
					if ( is_bool( $value ) ) {
						echo $value ? 'true' : 'false';
					} else {
						echo esc_html( $value );
					}
					echo '</li>';
				}
			}
			echo '</ul>';
		} else {
			echo '<li>';
			echo esc_html( $entry );
			echo '</li>';
		}
	}

}
