<?php
/**
 * Plugin Deactivator
 *
 * Runs on plugin deactivation.
 *
 * @package Advanced_Image_Optimization_Kaddora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AIOK_Deactivator {

	/**
	 * Run deactivation tasks.
	 *
	 * @return void
	 */
	public static function deactivate() {
		self::clear_scheduled_events();
		self::flush_rewrite_rules_if_needed();
	}

	/**
	 * Clear scheduled cron events used by the plugin.
	 *
	 * @return void
	 */
	private static function clear_scheduled_events() {
		$scheduled_hooks = array(
			'aiok_bulk_optimization_cron',
			'aiok_cleanup_logs_cron',
			'aiok_regenerate_formats_cron',
		);

		foreach ( $scheduled_hooks as $hook ) {
			$timestamp = wp_next_scheduled( $hook );

			while ( $timestamp ) {
				wp_unschedule_event( $timestamp, $hook );
				$timestamp = wp_next_scheduled( $hook );
			}
		}
	}

	/**
	 * Flush rewrite rules if plugin later adds rewrite-based delivery.
	 *
	 * For V1 this is mostly future-safe.
	 *
	 * @return void
	 */
	private static function flush_rewrite_rules_if_needed() {
		flush_rewrite_rules();
	}
}