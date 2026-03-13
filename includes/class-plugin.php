<?php
/**
 * Main Plugin Controller
 *
 * Minimal temporary controller for early development.
 *
 * @package Advanced_Image_Optimization_Kaddora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AIOK_Plugin {

	/**
	 * Admin module instance.
	 *
	 * @var AIOK_Admin|null
	 */
	private $admin = null;

	/**
	 * Settings module instance.
	 *
	 * @var AIOK_Settings|null
	 */
	private $settings = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->load_modules();
		$this->register_hooks();
	}

	/**
	 * Load only modules that are currently available.
	 *
	 * @return void
	 */
	private function load_modules() {
		if ( class_exists( 'AIOK_Admin' ) ) {
			$this->admin = new AIOK_Admin();
		}

		if ( class_exists( 'AIOK_Settings' ) ) {
			$this->settings = new AIOK_Settings();
		}
	}

	/**
	 * Register WordPress hooks for loaded modules only.
	 *
	 * @return void
	 */
	private function register_hooks() {
		if ( is_admin() && $this->admin instanceof AIOK_Admin ) {
			add_action( 'admin_menu', array( $this->admin, 'register_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_assets' ) );
		}

		if ( $this->settings instanceof AIOK_Settings ) {
			add_action( 'admin_init', array( $this->settings, 'register_settings' ) );
		}
	}

	/**
	 * Run plugin.
	 *
	 * @return void
	 */
	public function run() {
		// Reserved for future runtime logic.
	}
}