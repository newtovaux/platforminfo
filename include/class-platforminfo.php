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
				<a href="#site"><?php esc_html_e( 'Site', 'platforminfo' ); ?></a> | <a href="#env"><?php esc_html_e( 'Environment', 'platforminfo' ); ?></a> | <a href="#php">PHP</a> | <a href="#cron">WPCron</a> | <a href="#ext"><?php esc_html_e( 'Extensions', 'platforminfo' ); ?></a> | <a href="#const"><?php esc_html_e( 'Constants', 'platforminfo' ); ?></a> | <a href="#opcache">OPcache</a>
			</p>
			<h2><a id="site"><?php esc_html_e( 'Site', 'platforminfo' ); ?></a></h2>
			<p><?php esc_html_e( 'Key details of your site\'s domain, and where it is located on the server.', 'platforminfo' ); ?></p>
			<table class="platforminfo">
				<tr class="platforminfo_striped">
					<td><?php esc_html_e( 'URL', 'platforminfo' ); ?></td>
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
							esc_html( (string) $e ),
							esc_html( (string) $v )
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
			<?php if ( true === function_exists( '_get_cron_array' ) ) { ?>
			<h2><a id="htaccess"><?php esc_html_e( '.htaccess', 'platforminfo' ); ?></a></h2>
			<?php
				$cwd = getcwd();

				// optential .htaccess file locations
				$locations = [
					realpath($cwd . '/..'),
					realpath($cwd . '/../..'),
				];

				$confirmed_locs = [];
				// search for .htaccess files
				foreach ($locations as $location)
				{
					if (file_exists($location . '/.htaccess'))
					{
						array_push($confirmed_locs, $location);
					}
				}
			?>
			<p>Searched locations: 
				<?php
					foreach ($locations as $location)
					{
						echo "$location, ";
					}
				?>
			</p>
			<?php
				if (null !== $confirmed_locs)
				{
					foreach($confirmed_locs as $loc)
					{
						printf(
							'<p><b>%s</b><br /><pre>%s</pre></p>',
							$loc . '/.htaccess',
							file_get_contents($loc . '/.htaccess')
						);
					}
					
				} else {
					printf('<p>Unable to locate .htaccess file.');
				}
			?>
			<h2><a id="cron"><?php esc_html_e( 'WordPress Cron', 'platforminfo' ); ?></a></h2>
			<p>Event scheduler is 
				<?php
				if ( false === _get_cron_array() ) {
					esc_html_e( 'not running', 'platforminfo' );
				} else {
					esc_html_e( 'running', 'platforminfo' );
				}
				?>
				and WP-Cron is
				<?php
				// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				if ( ( true === defined( 'DISABLE_WP_CRON' ) ) && ( true == DISABLE_WP_CRON ) ) {
					esc_html_e( 'disabled', 'platforminfo' );
				} else {
					esc_html_e( 'enabled', 'platforminfo' );
				}
				?>
.			</p>
			<p><?php esc_html_e( 'List of all scheduled events', 'platforminfo' ); ?>:</p>
			<table class="wp-list-table widefat fixed striped table-view-list">
				<thead>
					<tr>
						<th class="manage-column" width="40%"><?php esc_html_e( 'Hook', 'platforminfo' ); ?></th>
						<th class="manage-column" width="30%"><?php esc_html_e( 'Recurrence', 'platforminfo' ); ?></th>
						<th class="manage-column" width="30%"><?php esc_html_e( 'Next scheduled run', 'platforminfo' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$crons = _get_cron_array();
					if ( false !== $crons ) {
						foreach ( $crons as $key => $value ) {
							foreach ( $value as $k => $v ) {
								printf(
									'<tr><td>%s</td><td>%s</td><td>%s</td></tr>',
									esc_html( $k ),
									esc_html( self::print_schedule( array_values( $v )[0]['schedule'] ) ),
									esc_html( self::print_next_schedule( $k ) )
								);
							}
						}
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
							esc_html( self::display_constant( $value ) )
						);
					}
					?>
					</tbody>
				</table>
			</div>
			<h2><a id="opcache"><?php esc_html_e( 'PHP OPcache', 'platforminfo' ); ?></a></h2>
			<p><?php esc_html_e( 'OPcache improves PHP performance by storing precompiled script bytecode in shared memory, thereby removing the need for PHP to load and parse scripts on each request.', 'platforminfo' ); ?></p>
			<?php
			if ( false === extension_loaded( 'Zend OPcache' ) ) {
				if ( false === function_exists( 'dl' ) ) {
					?>
				<div class="notice notice-error"><p><?php esc_html_e( 'You do not have the Zend OPcache extension loaded. Unable to load Zend OPcache extension.', 'platforminfo' ); ?></p></div>
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
					echo '<h3>OPcache ' . esc_html__( 'Status', 'platforminfo' ) . '</h3>';
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
	 * Print versions of WordPress schedules
	 *
	 * @param mixed $entry hourly|twicedaily|daily|weekly.
	 * @since 1.1.7
	 *
	 * @return string
	 */
	public static function print_schedule( $entry ): string {
		switch ( $entry ) {
			case 'hourly':
				return __( 'Hourly', 'platforminfo' );
			case 'twicedaily':
				return __( 'Twice daily', 'platforminfo' );
			case 'daily':
				return __( 'Daily', 'platforminfo' );
			case 'weekly':
				return __( 'Weekly', 'platforminfo' );
			case '':
				return __( 'Non-repeating', 'platforminfo' );
			default:
				return (string) $entry;
		}
	}

	/**
	 * Display constant value
	 *
	 * @param mixed $value contant value.
	 * @since 1.1.12
	 *
	 * @return string
	 */
	public static function display_constant( $value ): string {
		if ( is_bool( $value ) ) {
			return $value ? __( 'true', 'platforminfo' ) : __( 'false', 'platforminfo' );
		}
		if ( is_null( $value ) ) {
			return __( 'null', 'platforminfo' );
		}
		if ( is_array( $value ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return '<pre>' . print_r( $value, true ) . '</pre>';
		}
		return esc_html( (string) $value );
	}

	/**
	 * Print next schedule
	 *
	 * @param mixed $entry cron entry.
	 * @since 1.1.11
	 *
	 * @return string
	 */
	public static function print_next_schedule( $entry ): string {
		if ( ! is_string( $entry ) ) {
			return '';
		}
		$next_schedule = wp_next_scheduled( $entry );
		if ( false === $next_schedule ) {
			return '';
		}
		$next_schedule_ts = wp_date( 'Y-m-d H:i:s', $next_schedule );
		if ( false === $next_schedule_ts ) {
			return '';
		} else {
			if ( $next_schedule < time() ) {
				return sprintf( 'Overdue by %s: %s (%s)', self::secondstohuman( time() - $next_schedule ), $next_schedule_ts, __( 'UTC' ) );
			} else {
				return sprintf( 'Due in %s: %s (%s)', self::secondstohuman( $next_schedule - time() ), $next_schedule_ts, __( 'UTC' ) );
			}
		}
	}

	/**
	 * Convert seconds to human readable time
	 *
	 * @param int $seconds seconds difference.
	 * @since 1.2.1
	 *
	 * @return string
	 */
	public static function secondstohuman( $seconds ): string {
		$ret = '';
		if ( $seconds >= 86400 ) {
			$days     = floor( $seconds / 86400 );
			$ret     .= sprintf( '%d day%s', $days, $days > 1 ? 's' : '' );
			$seconds -= $days * 86400;
		}
		if ( $seconds >= 3600 ) {
			$hours    = floor( $seconds / 3600 );
			$ret     .= sprintf( ' %d hour%s', $hours, $hours > 1 ? 's' : '' );
			$seconds -= $hours * 3600;
		}
		if ( $seconds >= 60 ) {
			$minutes  = floor( $seconds / 60 );
			$ret     .= sprintf( ' %d minute%s', $minutes, $minutes > 1 ? 's' : '' );
			$seconds -= $minutes * 60;
		}
		if ( $seconds > 0 ) {
			$ret .= sprintf( ' %d second%s', $seconds, $seconds > 1 ? 's' : '' );
		}
		return $ret;
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
					printf( '<b>%s</b>: ', esc_html( (string) $key ) );
					self::recursive_ulli( $value );
					echo '</li>';
				} else {
					echo '<li>';
					printf( '%s = ', esc_html( (string) $key ) );
					if ( is_bool( $value ) ) {
						if ( true === $value ) {
							echo esc_html__( 'true', 'platforminfo' );
						} else {
							echo esc_html__( 'false', 'platforminfo' );
						}
					} else {
						echo esc_html( (string) $value );
					}
					echo '</li>';
				}
			}
			echo '</ul>';
		} else {
			echo '<li>';
			echo esc_html( (string) $entry );
			echo '</li>';
		}
	}

}
