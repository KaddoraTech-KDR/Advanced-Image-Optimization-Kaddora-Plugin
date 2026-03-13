<?php
/**
 * Plugin Activator
 *
 * Runs on plugin activation.
 *
 * @package Advanced_Image_Optimization_Kaddora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AIOK_Activator {

	/**
	 * Run activation tasks.
	 *
	 * @return void
	 */
	public static function activate() {
		self::create_storage_directories();
		self::create_protection_files();
		self::set_default_options();
	}

	/**
	 * Create required storage directories.
	 *
	 * @return void
	 */
	private static function create_storage_directories() {
		$directories = array(
			AIOK_STORAGE_PATH,
			AIOK_LOGS_PATH,
			AIOK_BACKUPS_PATH,
		);

		foreach ( $directories as $directory ) {
			if ( ! file_exists( $directory ) ) {
				wp_mkdir_p( $directory );
			}
		}
	}

	/**
	 * Create basic protection files in storage directories.
	 *
	 * @return void
	 */
	private static function create_protection_files() {
		$directories = array(
			AIOK_STORAGE_PATH,
			AIOK_LOGS_PATH,
			AIOK_BACKUPS_PATH,
		);

		foreach ( $directories as $directory ) {
			$index_file = trailingslashit( $directory ) . 'index.php';

			if ( ! file_exists( $index_file ) ) {
				$content = "<?php\n// Silence is golden.\n";
				wp_filesystem_put_contents_safe( $index_file, $content );
			}
		}
	}

	/**
	 * Set default plugin options.
	 *
	 * @return void
	 */
	private static function set_default_options() {
		$defaults = array(
			'auto_optimize'         => 1,
			'backup_original'       => 1,
			'compression_enabled'   => 1,
			'compression_level'     => 'balanced',
			'jpeg_quality'          => 82,
			'png_quality'           => 82,
			'generate_webp'         => 1,
			'generate_avif'         => 0,
			'lazyload_enabled'      => 1,
			'cdn_enabled'           => 0,
			'cdn_url'               => '',
			'keep_logs'             => 1,
		);

		$option_name = 'aiok_settings';
		$saved       = get_option( $option_name );

		if ( false === $saved ) {
			add_option( $option_name, $defaults );
			return;
		}

		if ( is_array( $saved ) ) {
			$merged = wp_parse_args( $saved, $defaults );
			update_option( $option_name, $merged );
		}
	}
}

if ( ! function_exists( 'wp_filesystem_put_contents_safe' ) ) {
	/**
	 * Safely write contents to a file.
	 *
	 * @param string $file    Absolute file path.
	 * @param string $content File content.
	 * @return bool
	 */
	function wp_filesystem_put_contents_safe( $file, $content ) {
		$dir = dirname( $file );

		if ( ! file_exists( $dir ) ) {
			wp_mkdir_p( $dir );
		}

		$handle = @fopen( $file, 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		if ( ! $handle ) {
			return false;
		}

		$written = fwrite( $handle, $content ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

		return false !== $written;
	}
}