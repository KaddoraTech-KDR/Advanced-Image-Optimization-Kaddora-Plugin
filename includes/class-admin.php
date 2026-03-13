<?php
/**
 * Admin Handler
 *
 * Registers admin menu, loads assets, and renders admin pages.
 *
 * @package Advanced_Image_Optimization_Kaddora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AIOK_Admin {

	/**
	 * Plugin capability.
	 *
	 * @var string
	 */
	private $capability = 'manage_options';

	/**
	 * Plugin menu slug.
	 *
	 * @var string
	 */
	private $menu_slug = 'aiok-dashboard';

	/**
	 * Register admin menu and submenu pages.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_menu_page(
			__( 'Kaddora Optimization', 'advanced-image-optimization-kaddora' ),
			__( 'Kaddora Optimization', 'advanced-image-optimization-kaddora' ),
			$this->capability,
			$this->menu_slug,
			array( $this, 'render_dashboard_page' ),
			'dashicons-format-image',
			56
		);

		add_submenu_page(
			$this->menu_slug,
			__( 'Dashboard', 'advanced-image-optimization-kaddora' ),
			__( 'Dashboard', 'advanced-image-optimization-kaddora' ),
			$this->capability,
			$this->menu_slug,
			array( $this, 'render_dashboard_page' )
		);

		add_submenu_page(
			$this->menu_slug,
			__( 'Settings', 'advanced-image-optimization-kaddora' ),
			__( 'Settings', 'advanced-image-optimization-kaddora' ),
			$this->capability,
			'aiok-settings',
			array( $this, 'render_settings_page' )
		);

		add_submenu_page(
			$this->menu_slug,
			__( 'Bulk Optimization', 'advanced-image-optimization-kaddora' ),
			__( 'Bulk Optimization', 'advanced-image-optimization-kaddora' ),
			$this->capability,
			'aiok-bulk-optimizer',
			array( $this, 'render_bulk_optimizer_page' )
		);

		add_submenu_page(
			$this->menu_slug,
			__( 'Logs', 'advanced-image-optimization-kaddora' ),
			__( 'Logs', 'advanced-image-optimization-kaddora' ),
			$this->capability,
			'aiok-logs',
			array( $this, 'render_logs_page' )
		);
	}

	/**
	 * Enqueue admin CSS and JS.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		$allowed_hooks = array(
			'toplevel_page_aiok-dashboard',
			'kaddora-optimization_page_aiok-settings',
			'kaddora-optimization_page_aiok-bulk-optimizer',
			'kaddora-optimization_page_aiok-logs',
		);

		if ( ! in_array( $hook_suffix, $allowed_hooks, true ) ) {
			return;
		}

		wp_enqueue_style(
			'aiok-admin',
			AIOK_ASSETS_URL . 'css/admin.css',
			array(),
			AIOK_VERSION
		);

		wp_enqueue_script(
			'aiok-admin',
			AIOK_ASSETS_URL . 'js/admin.js',
			array( 'jquery' ),
			AIOK_VERSION,
			true
		);

		wp_localize_script(
			'aiok-admin',
			'aiokAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'aiok_admin_nonce' ),
			)
		);

		if ( 'kaddora-optimization_page_aiok-bulk-optimizer' === $hook_suffix ) {
			wp_enqueue_script(
				'aiok-bulk-optimizer',
				AIOK_ASSETS_URL . 'js/bulk-optimizer.js',
				array( 'jquery' ),
				AIOK_VERSION,
				true
			);

			wp_localize_script(
				'aiok-bulk-optimizer',
				'aiokBulkOptimizer',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'aiok_bulk_optimize_nonce' ),
					'action'  => 'aiok_bulk_optimize',
				)
			);
		}
	}

	/**
	 * Render dashboard page.
	 *
	 * @return void
	 */
	public function render_dashboard_page() {
		$this->authorize();
		$this->load_view( 'dashboard.php' );
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		$this->authorize();
		$this->load_view( 'settings.php' );
	}

	/**
	 * Render bulk optimizer page.
	 *
	 * @return void
	 */
	public function render_bulk_optimizer_page() {
		$this->authorize();
		$this->load_view( 'bulk-optimizer.php' );
	}

	/**
	 * Render logs page.
	 *
	 * @return void
	 */
	public function render_logs_page() {
		$this->authorize();
		$this->load_view( 'logs.php' );
	}

	/**
	 * Load admin view file.
	 *
	 * @param string $view_file View file name.
	 * @return void
	 */
	private function load_view( $view_file ) {
		$view_path = AIOK_PLUGIN_PATH . 'admin/views/' . $view_file;

		if ( file_exists( $view_path ) ) {
			include $view_path;
			return;
		}

		echo '<div class="wrap"><h1>' . esc_html__( 'View not found.', 'advanced-image-optimization-kaddora' ) . '</h1></div>';
	}

	/**
	 * Ensure user has permission.
	 *
	 * @return void
	 */
	private function authorize() {
		if ( ! current_user_can( $this->capability ) ) {
			wp_die(
				esc_html__( 'You do not have permission to access this page.', 'advanced-image-optimization-kaddora' )
			);
		}
	}
}