<?php

/**
 * Plugin Name:       Advanced Image Optimization Kaddora Plugin
 * Description:       Optimize images with compression, WebP, AVIF, lazy loading, and CDN support.
 * Version:           1.0.0
 * Author:            Kaddora Tech
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       advanced-image-optimization-kaddora
 * Domain Path:       /languages
 *
 * @package Advanced_Image_Optimization_Kaddora
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Plugin version.
 */
define('AIOK_VERSION', '1.0.0');

/**
 * Main plugin file path.
 */
define('AIOK_PLUGIN_FILE', __FILE__);

/**
 * Plugin basename.
 */
define('AIOK_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Plugin absolute path.
 */
define('AIOK_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Plugin URL.
 */
define('AIOK_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Assets URL.
 */
define('AIOK_ASSETS_URL', AIOK_PLUGIN_URL . 'assets/');

/**
 * Storage path.
 */
define('AIOK_STORAGE_PATH', AIOK_PLUGIN_PATH . 'storage/');

/**
 * Logs path.
 */
define('AIOK_LOGS_PATH', AIOK_STORAGE_PATH . 'logs/');

/**
 * Backups path.
 */
define('AIOK_BACKUPS_PATH', AIOK_STORAGE_PATH . 'backups/');

/**
 * Load plugin translations.
 *
 * @return void
 */
function aiok_load_textdomain()
{
	load_plugin_textdomain(
		'advanced-image-optimization-kaddora',
		false,
		dirname(AIOK_PLUGIN_BASENAME) . '/languages'
	);
}
add_action('plugins_loaded', 'aiok_load_textdomain');

/**
 * Include only currently available files.
 *
 * Keep this minimal until other modules are ready.
 *
 * @return void
 */
function aiok_include_files()
{
	$files = array(
		'includes/helpers.php',
		'includes/class-activator.php',
		'includes/class-deactivator.php',
		'includes/class-admin.php',
		'includes/class-settings.php',
		'includes/class-plugin.php',
		'includes/class-media.php',
		'includes/class-optimizer.php',
		'includes/class-converter.php',
		'includes/class-lazyload.php',
		'includes/class-cdn.php',
		'includes/class-bulk-optimizer.php',
		'includes/class-logger.php',
	);

	foreach ($files as $file) {
		$path = AIOK_PLUGIN_PATH . $file;

		if (file_exists($path)) {
			require_once $path;
		}
	}
}
aiok_include_files();

/**
 * Plugin activation callback.
 *
 * @return void
 */
function aiok_activate_plugin()
{
	if (class_exists('AIOK_Activator')) {
		AIOK_Activator::activate();
	}
}
register_activation_hook(__FILE__, 'aiok_activate_plugin');

/**
 * Plugin deactivation callback.
 *
 * @return void
 */
function aiok_deactivate_plugin()
{
	if (class_exists('AIOK_Deactivator')) {
		AIOK_Deactivator::deactivate();
	}
}
register_deactivation_hook(__FILE__, 'aiok_deactivate_plugin');

/**
 * Run the plugin.
 *
 * @return void
 */
function aiok_run_plugin()
{
	if (class_exists('AIOK_Plugin')) {
		$plugin = new AIOK_Plugin();
		$plugin->run();
	}
}
add_action('plugins_loaded', 'aiok_run_plugin', 20);
