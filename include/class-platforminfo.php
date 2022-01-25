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
 * @since    1.0.0
 */
final class Platforminfo {

	/**
	 * Psuedo constructor for static instance
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function instance() {
		new self();
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'platforminfo_load_admin_styles' ) );
			add_action( 'admin_menu', array( __CLASS__, 'platforminfo_options_page' ) );
			/**
			 * Ignore PLATFORMINFO_BASE, it is defined elsewhere.
			 *
			 * @psalm-suppress UndefinedConstant */
			add_filter( 'plugin_action_links_' . PLATFORMINFO_BASE, array( __CLASS__, 'action_links' ) );
		}
	}

	/**
	 * Add the styles and JavaScript
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function platforminfo_load_admin_styles() {
		wp_enqueue_style( 'platforminfo', plugins_url( 'platforminfo', '_FILE_' ) . '/admin/css/platforminfo.css', array(), '1.0' );
		wp_enqueue_script( 'platforminfo', plugins_url( 'platforminfo', '_FILE_' ) . '/admin/js/platforminfo.js', array(), '1.0', true );
	}

	/**
	 * Add the additional links on the plugins list
	 *
	 * @since 1.0.9
	 *
	 * @param array $links Array of action links.
	 *
	 * @return array
	 */
	public static function action_links( $links ) {
		// Build and escape the URL.
		$url = esc_url(
			add_query_arg(
				'page',
				'platforminfo',
				get_admin_url() . 'admin.php'
			)
		);
		// Create the link.
		$settings_link = sprintf( '<a href="%s">%s</a>', $url, esc_html__( 'Info', 'platforminfo' ) );
		// Adds the link to the end of the array.
		array_push(
			$links,
			$settings_link
		);
		return $links;
	}

	/**
	 * Add menu item
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function platforminfo_options_page() {
		add_menu_page(
			'PlatformInfo', // $page_title
			'PlatformInfo', // $menu_title
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
	 * @since 1.0.0
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
				'setting'     => 'file_uploads',
				'description' => __( 'Whether or not to allow HTTP file uploads', 'platforminfo' ),
				'type'        => 'bool',
			),
			array(
				'setting'     => 'post_max_size',
				'description' => __( 'Sets the maximum size of POST data allowed. This setting also affects file uploads', 'platforminfo' ),
			),
			array(
				'setting'     => 'upload_max_filesize',
				'description' => __( 'The maximum size of an uploaded file', 'platforminfo' ),
			),
			array(
				'setting'     => 'memory_limit',
				'description' => __( 'This sets the maximum amount of memory in bytes that a script is allowed to allocate', 'platforminfo' ),
			),
			array(
				'setting'     => 'disable_functions',
				'description' => __( 'This directive allows you to disable certain functions', 'platforminfo' ),
				'type'        => 'array',
			),
			array(
				'setting'     => 'max_execution_time',
				'description' => __( 'This sets the maximum time in seconds a script is allowed to run before it is terminated by the parser', 'platforminfo' ),
			),
			array(
				'setting'     => 'display_errors',
				'description' => __( 'This determines whether errors should be printed to the screen as part of the output or if they should be hidden from the user', 'platforminfo' ),
				'type'        => 'bool',
			),
		);
		?>
		<div class="wrap">
			<h1>
				<?php
					echo esc_html( get_admin_page_title() );
				?>
			</h1>
			<p>
				<a href="#site"><?php esc_html_e( 'Site', 'platforminfo' ); ?></a> | <a href="#env"><?php esc_html_e( 'Environment', 'platforminfo' ); ?></a> | <a href="#php">PHP</a> | <a href="#ext"><?php esc_html_e( 'Extensions', 'platforminfo' ); ?></a> | <a href="#const"><?php esc_html_e( 'Constants', 'platforminfo' ); ?></a> | <a href="#opcache">OPcache</a>
			</p>
			<h2><a id="site"><?php esc_html_e( 'Site', 'platforminfo' ); ?></a></h2>
			<p><?php esc_html_e( 'Key details of your site\'s domain, and where it is located on the server.', 'platforminfo' ); ?></p>
			<table class="platforminfo">
				<tr class="platforminfo_striped">
					<td><?php esc_html_e( 'URL' ); ?></td>
					<td><?php echo esc_html( site_url() ); ?> 
						<a href="#" onclick="clipboard(this)" data-item="<?php echo esc_attr( site_url() ); ?>" class="platform_clipboard"><span class="dashicons dashicons-clipboard"></span></a>
					</td>
				</tr>
				<tr class="platforminfo_striped">
					<td>
						WordPress Home Path</td><td><?php echo esc_html( get_home_path() ); ?>
						<a href="#" onclick="clipboard(this)" data-item="<?php echo esc_attr( get_home_path() ); ?>" class="platform_clipboard"><span class="dashicons dashicons-clipboard"></span></a>
					</td>
				</tr>
			</table>
			<h2><a id="env"><?php esc_html_e( 'Environment', 'platforminfo' ); ?></a></h2>
			<p><?php esc_html_e( 'Environment variables are dynamic-named values that can affect the way running processes will behave.', 'platforminfo' ); ?></p>
			<button type="button" class="platforminfo_collapsible"><?php esc_html_e( 'Environment details', 'platforminfo' ); ?></button>
			<div class="platforminfo_content">
				<table class="wp-list-table widefat fixed striped table-view-list">
					<thead>
						<tr>
							<th class="manage-column"><?php esc_html_e( 'Environment variable', 'platforminfo' ); ?></th>
							<th class="manage-column"><?php esc_html_e( 'Value', 'platforminfo' ); ?></th>
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
			</div>
			<h2><a id="php"><?php esc_html_e( 'PHP', 'platforminfo' ); ?></a></h2>
			<p>
				PHP <?php esc_html_e( 'version', 'platforminfo' ); ?>: <?php echo esc_html( phpversion() ); ?> <a href="#" onclick="clipboard(this)" data-item="<?php echo esc_attr( phpversion() ); ?>" class="platform_clipboard"><span class="dashicons dashicons-clipboard"></span></a>, 
				php.ini: 
				<?php
				if ( false === php_ini_loaded_file() ) {
					esc_html_e( 'None', 'platforminfo' );
				} else {
					echo esc_html( php_ini_loaded_file() );
					?>
						<a href="#" onclick="clipboard(this)" data-item="<?php echo esc_attr( php_ini_loaded_file() ); ?>" class="platform_clipboard"><span class="dashicons dashicons-clipboard"></span></a>
					<?php
				}
				?>
			</p>
			<p>
				<?php esc_html_e( 'Scanned files', 'platforminfo' ); ?>: 
				<?php
				$inis = php_ini_scanned_files();
				if ( ( false === $inis ) || ( 0 === strlen( $inis ) ) ) {
					esc_html_e( 'None', 'platforminfo' );
				} else {
					echo esc_html( php_ini_scanned_files() );
					?>
					<a href="#" onclick="clipboard(this)" data-item="<?php echo esc_attr( php_ini_scanned_files() ); ?>" class="platform_clipboard"><span class="dashicons dashicons-clipboard"></span></a>
				<?php } ?>
			</p>
			<table class="wp-list-table widefat fixed striped table-view-list">
				<thead>
					<tr>
						<th class="manage-column" width=”20%”>Common important php.ini settings</th>
						<th class="manage-column" width=”20%”>Value</th>
						<th class="manage-column" width=”60%”>Description</th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $settings_to_display as $setting ) {
					switch ( $php_ini_settings[ $setting['setting'] ]['local_value'] ) {
						case '-1':
							printf(
								'<tr><td>%s</td><td>-1 (%s)</td><td>%s</td></tr>',
								esc_html( $setting['setting'] ),
								esc_html__( 'Unlimited by PHP', 'platforminfo' ),
								esc_html( $setting['description'] )
							);
							break;
						case null:
						case '':
							printf(
								'<tr><td>%s</td><td>(%s)</td><td>%s</td></tr>',
								esc_html( $setting['setting'] ),
								esc_html__( 'Not set in php.ini', 'platforminfo' ),
								esc_html( $setting['description'] )
							);
							break;
						default:
							if ( array_key_exists( 'type', $setting ) && ( 'bool' === $setting['type'] ) ) {
								printf(
									'<tr><td>%s</td><td>%s (%s)</td><td>%s</td></tr>',
									esc_html( $setting['setting'] ),
									esc_html( $php_ini_settings[ $setting['setting'] ]['local_value'] ),
									$php_ini_settings[ $setting['setting'] ]['local_value'] ? 'True' : 'False',
									esc_html( $setting['description'] )
								);
							} elseif ( array_key_exists( 'type', $setting ) && ( 'array' === $setting['type'] ) ) {
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
			<?php if ( true === function_exists('_get_cron_array') ) { ?>
			<h2><a id="cron">PHP <?php esc_html_e( 'WordPress Cron', 'platforminfo' ); ?></a></h2>
			<p><?php esc_html_e( 'List of all scheduled WordPress cron jobs', 'platforminfo' ); ?>:</p>
			<table class="wp-list-table widefat fixed striped table-view-list">
				<thead>
					<tr>
						<th class="manage-column" width=”50%”>Event name</th>
						<th class="manage-column" width=”50%”>Schedule</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach( _get_cron_array() as $key => $value ) {
							foreach ( $value as $k => $v)
							printf(
								'<tr><td>%s</td><td>%s</td></tr>',
								esc_html__( $k ),
								esc_html__( array_values( $v )[0]['schedule'] )
							);
						}
					?>
				</tbody>
			</table>
			<?php } ?>
			<h2><a id="ext">PHP <?php esc_html_e( 'Extensions', 'platforminfo' ); ?></a></h2>
			<p><?php esc_html_e( 'List of all PHP modules compiled and loaded', 'platforminfo' ); ?>:</p>
			<div style="border: 1px solid #c3c4c7; background-color: #ffffff; padding: 8px 10px">
				<ul>
				<?php
				foreach ( get_loaded_extensions() as $ext ) {
					printf( '<li style="display: inline-block; padding-right: 10px;">%s</li>', esc_html( $ext ) );
				}
				?>
				</ul>
			</div>
			<h2><a id="const">PHP <?php esc_html_e( 'Constants (User)', 'platforminfo' ); ?></a></h2>
			<p><?php esc_html_e( 'A constant is an identifier (name) for a simple value. As the name suggests, that value cannot change during the execution of the script.', 'platforminfo' ); ?></p>
			<button type="button" class="platforminfo_collapsible">Constants  <?php esc_html_e( 'details', 'platforminfo' ); ?></button>
			<div class="platforminfo_content">
				<table class="wp-list-table widefat fixed striped table-view-list">
					<thead>
						<tr>
							<th class="manage-column" width=”50%”>Constant</th>
							<th class="manage-column" width=”50%”>Value</th>
						</tr>
					</thead>
					<tbody>
					<?php
					foreach ( get_defined_constants( true )['user'] as $key => $value ) {
						printf(
							'<tr><td>%s</td><td>%s</td></tr>',
							esc_html( $key ),
							is_string( $value ) ? esc_html( $value ) : esc_html__( 'None' )
						);
					}
					?>
					</tbody>
				</table>
			</div>
			<h2><a id="opcache"><?php esc_html_e( 'PHP OPcache', 'platforminfo' ); ?></a></h2>
			<p><?php esc_html_e( 'OPcache improves PHP performance by storing precompiled script bytecode in shared memory, thereby removing the need for PHP to load and parse scripts on each request.', 'platforminfo' ); ?></p>
			<?php
			if ( ! extension_loaded( 'Zend OPcache' ) ) {
				?>
			<div class="notice notice-error"><p><?php esc_html_e( 'You do not have the Zend OPcache extension loaded.', 'platforminfo' ); ?></p></div>
				<?php
				if ( function_exists( 'dl' ) === false ) {
					?>
				<div class="notice notice-error"><p><?php esc_html_e( 'Unable to load Zend OPcache extension.', 'platforminfo' ); ?></p></div>
			<?php } ?>
			<p><?php esc_html_e( 'OPcache not available.', 'platforminfo' ); ?></p>
			<?php } else { ?>
			<button type="button" class="platforminfo_collapsible">OPcache <?php esc_html_e( 'details', 'platforminfo' ); ?></button>
			<div class="platforminfo_content">
				<p>
				<?php
				if ( function_exists( 'opcache_get_configuration' ) ) {

					$config = opcache_get_configuration();
					echo '<h3>OPcache ' . esc_html__( 'Configuration', 'platforminfo' ) . '</h3>';
					self::recursive_ulli( $config );

					$status = opcache_get_status();
					unset( $status['scripts'] );
					echo '<h3>OPcache ' . esc_html__( 'Status' ) . '</h3>';
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
	 * @since 1.0.0
	 *
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
						if ( true === $value ) {
							echo esc_html__( 'true', 'platforminfo' );
						} else {
							echo esc_html__( 'false', 'platforminfo' );
						}
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
