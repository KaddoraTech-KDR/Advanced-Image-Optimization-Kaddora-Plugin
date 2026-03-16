<?php

/**
 * Settings Handler
 *
 * Registers plugin settings, sanitizes values, and renders settings fields.
 *
 * @package Advanced_Image_Optimization_Kaddora
 */

if (! defined('ABSPATH')) {
	exit;
}

class AIOK_Settings
{

	/**
	 * Option name in wp_options table.
	 *
	 * @var string
	 */
	private $option_name = 'aiok_settings';

	/**
	 * Settings group name.
	 *
	 * @var string
	 */
	private $settings_group = 'aiok_settings_group';

	/**
	 * Register plugin settings, sections, and fields.
	 *
	 * @return void
	 */
	public function register_settings()
	{
		register_setting(
			$this->settings_group,
			$this->option_name,
			array($this, 'sanitize_settings')
		);

		$this->register_general_section();
		$this->register_compression_section();
		$this->register_format_section();
		$this->register_delivery_section();
	}

	/**
	 * Get all settings.
	 *
	 * @return array
	 */
	public function get_settings()
	{
		$defaults = $this->get_default_settings();
		$saved    = get_option($this->option_name, array());

		if (! is_array($saved)) {
			$saved = array();
		}

		return wp_parse_args($saved, $defaults);
	}

	/**
	 * Get a single setting value.
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	public function get_setting($key, $default = null)
	{
		$settings = $this->get_settings();

		return isset($settings[$key]) ? $settings[$key] : $default;
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings()
	{
		return array(
			'auto_optimize'       => 1,
			'backup_original'     => 1,
			'compression_enabled' => 1,
			'compression_level'   => 'balanced',
			'jpeg_quality'        => 82,
			'png_quality'         => 82,
			'generate_webp'       => 1,
			'generate_avif'       => 0,
			'lazyload_enabled'    => 1,
			'cdn_enabled'         => 0,
			'cdn_url'             => '',
			'keep_logs'           => 1,
		);
	}

	/**
	 * Sanitize all plugin settings.
	 *
	 * @param array $input Raw input.
	 * @return array
	 */
	public function sanitize_settings($input)
	{
		$defaults = $this->get_default_settings();
		$input    = is_array($input) ? $input : array();
		$output   = array();

		$output['auto_optimize']       = ! empty($input['auto_optimize']) ? 1 : 0;
		$output['backup_original']     = ! empty($input['backup_original']) ? 1 : 0;
		$output['compression_enabled'] = ! empty($input['compression_enabled']) ? 1 : 0;
		$output['generate_webp']       = ! empty($input['generate_webp']) ? 1 : 0;
		$output['generate_avif']       = ! empty($input['generate_avif']) ? 1 : 0;
		$output['lazyload_enabled']    = ! empty($input['lazyload_enabled']) ? 1 : 0;
		$output['cdn_enabled']         = ! empty($input['cdn_enabled']) ? 1 : 0;
		$output['keep_logs']           = ! empty($input['keep_logs']) ? 1 : 0;

		$allowed_levels = array('lossless', 'balanced', 'aggressive');
		$compression    = isset($input['compression_level']) ? sanitize_text_field($input['compression_level']) : $defaults['compression_level'];

		$output['compression_level'] = in_array($compression, $allowed_levels, true)
			? $compression
			: $defaults['compression_level'];

		$jpeg_quality = isset($input['jpeg_quality']) ? absint($input['jpeg_quality']) : $defaults['jpeg_quality'];
		$png_quality  = isset($input['png_quality']) ? absint($input['png_quality']) : $defaults['png_quality'];

		$output['jpeg_quality'] = min(100, max(1, $jpeg_quality));
		$output['png_quality']  = min(100, max(1, $png_quality));

		$cdn_url = isset($input['cdn_url']) ? esc_url_raw(trim($input['cdn_url'])) : '';
		$output['cdn_url'] = untrailingslashit($cdn_url);

		return wp_parse_args($output, $defaults);
	}

	/**
	 * Register general settings section.
	 *
	 * @return void
	 */
	private function register_general_section()
	{
		add_settings_section(
			'aiok_general_section',
			__('General Settings', 'advanced-image-optimization-kaddora'),
			array($this, 'render_general_section_description'),
			'aiok-settings'
		);

		add_settings_field(
			'auto_optimize',
			__('Auto Optimize on Upload', 'advanced-image-optimization-kaddora'),
			array($this, 'render_checkbox_field'),
			'aiok-settings',
			'aiok_general_section',
			array(
				'key'         => 'auto_optimize',
				'description' => __('Automatically optimize images when uploaded.', 'advanced-image-optimization-kaddora'),
			)
		);

		add_settings_field(
			'backup_original',
			__('Backup Original Images', 'advanced-image-optimization-kaddora'),
			array($this, 'render_checkbox_field'),
			'aiok-settings',
			'aiok_general_section',
			array(
				'key'         => 'backup_original',
				'description' => __('Save original images before optimization.', 'advanced-image-optimization-kaddora'),
			)
		);

		add_settings_field(
			'keep_logs',
			__('Keep Optimization Logs', 'advanced-image-optimization-kaddora'),
			array($this, 'render_checkbox_field'),
			'aiok-settings',
			'aiok_general_section',
			array(
				'key'         => 'keep_logs',
				'description' => __('Store optimization and error logs.', 'advanced-image-optimization-kaddora'),
			)
		);
	}

	/**
	 * Register compression settings section.
	 *
	 * @return void
	 */
	private function register_compression_section()
	{
		add_settings_section(
			'aiok_compression_section',
			__('Compression Settings', 'advanced-image-optimization-kaddora'),
			array($this, 'render_compression_section_description'),
			'aiok-settings'
		);

		add_settings_field(
			'compression_enabled',
			__('Enable Compression', 'advanced-image-optimization-kaddora'),
			array($this, 'render_checkbox_field'),
			'aiok-settings',
			'aiok_compression_section',
			array(
				'key'         => 'compression_enabled',
				'description' => __('Enable image compression during optimization.', 'advanced-image-optimization-kaddora'),
			)
		);

		add_settings_field(
			'compression_level',
			__('Compression Level', 'advanced-image-optimization-kaddora'),
			array($this, 'render_select_field'),
			'aiok-settings',
			'aiok_compression_section',
			array(
				'key'         => 'compression_level',
				'options'     => array(
					'lossless'   => __('Lossless', 'advanced-image-optimization-kaddora'),
					'balanced'   => __('Balanced', 'advanced-image-optimization-kaddora'),
					'aggressive' => __('Aggressive', 'advanced-image-optimization-kaddora'),
				),
				'description' => __('Choose how strongly images should be compressed.', 'advanced-image-optimization-kaddora'),
			)
		);

		add_settings_field(
			'jpeg_quality',
			__('JPEG Quality', 'advanced-image-optimization-kaddora'),
			array($this, 'render_number_field'),
			'aiok-settings',
			'aiok_compression_section',
			array(
				'key'         => 'jpeg_quality',
				'min'         => 1,
				'max'         => 100,
				'description' => __('Set JPEG quality from 1 to 100.', 'advanced-image-optimization-kaddora'),
			)
		);

		add_settings_field(
			'png_quality',
			__('PNG Quality', 'advanced-image-optimization-kaddora'),
			array($this, 'render_number_field'),
			'aiok-settings',
			'aiok_compression_section',
			array(
				'key'         => 'png_quality',
				'min'         => 1,
				'max'         => 100,
				'description' => __('Set PNG quality from 1 to 100.', 'advanced-image-optimization-kaddora'),
			)
		);
	}

	/**
	 * Register modern format settings section.
	 *
	 * @return void
	 */
	private function register_format_section()
	{
		add_settings_section(
			'aiok_format_section',
			__('WebP and AVIF Settings', 'advanced-image-optimization-kaddora'),
			array($this, 'render_format_section_description'),
			'aiok-settings'
		);

		add_settings_field(
			'generate_webp',
			__('Generate WebP', 'advanced-image-optimization-kaddora'),
			array($this, 'render_checkbox_field'),
			'aiok-settings',
			'aiok_format_section',
			array(
				'key'         => 'generate_webp',
				'description' => __('Create WebP versions of supported images.', 'advanced-image-optimization-kaddora'),
			)
		);

		add_settings_field(
			'generate_avif',
			__('Generate AVIF', 'advanced-image-optimization-kaddora'),
			array($this, 'render_checkbox_field'),
			'aiok-settings',
			'aiok_format_section',
			array(
				'key'         => 'generate_avif',
				'description' => __('Create AVIF versions when server support is available.', 'advanced-image-optimization-kaddora'),
			)
		);
	}

	/**
	 * Register delivery settings section.
	 *
	 * @return void
	 */
	private function register_delivery_section()
	{
		add_settings_section(
			'aiok_delivery_section',
			__('Delivery Settings', 'advanced-image-optimization-kaddora'),
			array($this, 'render_delivery_section_description'),
			'aiok-settings'
		);

		add_settings_field(
			'lazyload_enabled',
			__('Enable Lazy Loading', 'advanced-image-optimization-kaddora'),
			array($this, 'render_checkbox_field'),
			'aiok-settings',
			'aiok_delivery_section',
			array(
				'key'         => 'lazyload_enabled',
				'description' => __('Apply lazy loading to frontend images.', 'advanced-image-optimization-kaddora'),
			)
		);

		add_settings_field(
			'cdn_enabled',
			__('Enable CDN URL Rewriting', 'advanced-image-optimization-kaddora'),
			array($this, 'render_checkbox_field'),
			'aiok-settings',
			'aiok_delivery_section',
			array(
				'key'         => 'cdn_enabled',
				'description' => __('Rewrite image URLs to your CDN domain.', 'advanced-image-optimization-kaddora'),
			)
		);

		add_settings_field(
			'cdn_url',
			__('CDN Base URL', 'advanced-image-optimization-kaddora'),
			array($this, 'render_text_field'),
			'aiok-settings',
			'aiok_delivery_section',
			array(
				'key'         => 'cdn_url',
				'placeholder' => 'https://cdn.example.com',
				'description' => __('Enter the CDN base URL used to serve media files.', 'advanced-image-optimization-kaddora'),
			)
		);
	}

	/**
	 * Render general section description.
	 *
	 * @return void
	 */
	public function render_general_section_description()
	{
		echo '<p>' . esc_html__('Configure basic plugin behavior and logging options.', 'advanced-image-optimization-kaddora') . '</p>';
	}

	/**
	 * Render compression section description.
	 *
	 * @return void
	 */
	public function render_compression_section_description()
	{
		echo '<p>' . esc_html__('Control image compression and quality levels.', 'advanced-image-optimization-kaddora') . '</p>';
	}

	/**
	 * Render format section description.
	 *
	 * @return void
	 */
	public function render_format_section_description()
	{
		echo '<p>' . esc_html__('Enable next-generation image formats for better performance.', 'advanced-image-optimization-kaddora') . '</p>';
	}

	/**
	 * Render delivery section description.
	 *
	 * @return void
	 */
	public function render_delivery_section_description()
	{
		echo '<p>' . esc_html__('Control frontend delivery features such as lazy loading and CDN rewriting.', 'advanced-image-optimization-kaddora') . '</p>';
	}

	/**
	 * Render checkbox field.
	 *
	 * @param array $args Field args.
	 * @return void
	 */
	public function render_checkbox_field($args)
	{
		$key         = isset($args['key']) ? $args['key'] : '';
		$description = isset($args['description']) ? $args['description'] : '';
		$value       = $this->get_setting($key, 0);

?>
		<label for="<?php echo esc_attr($this->field_id($key)); ?>">
			<input
				type="checkbox"
				id="<?php echo esc_attr($this->field_id($key)); ?>"
				name="<?php echo esc_attr($this->field_name($key)); ?>"
				value="1"
				<?php checked(1, (int) $value); ?> />
			<?php if (! empty($description)) : ?>
				<span><?php echo esc_html($description); ?></span>
			<?php endif; ?>
		</label>
	<?php
	}

	/**
	 * Render text field.
	 *
	 * @param array $args Field args.
	 * @return void
	 */
	public function render_text_field($args)
	{
		$key         = isset($args['key']) ? $args['key'] : '';
		$description = isset($args['description']) ? $args['description'] : '';
		$placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
		$value       = $this->get_setting($key, '');

	?>
		<input
			type="text"
			id="<?php echo esc_attr($this->field_id($key)); ?>"
			name="<?php echo esc_attr($this->field_name($key)); ?>"
			value="<?php echo esc_attr($value); ?>"
			placeholder="<?php echo esc_attr($placeholder); ?>"
			class="regular-text" />
		<?php if (! empty($description)) : ?>
			<p class="description"><?php echo esc_html($description); ?></p>
		<?php endif; ?>
	<?php
	}

	/**
	 * Render number field.
	 *
	 * @param array $args Field args.
	 * @return void
	 */
	public function render_number_field($args)
	{
		$key         = isset($args['key']) ? $args['key'] : '';
		$min         = isset($args['min']) ? (int) $args['min'] : 0;
		$max         = isset($args['max']) ? (int) $args['max'] : 100;
		$description = isset($args['description']) ? $args['description'] : '';
		$value       = $this->get_setting($key, '');

	?>
		<input
			type="number"
			id="<?php echo esc_attr($this->field_id($key)); ?>"
			name="<?php echo esc_attr($this->field_name($key)); ?>"
			value="<?php echo esc_attr($value); ?>"
			min="<?php echo esc_attr($min); ?>"
			max="<?php echo esc_attr($max); ?>"
			class="small-text" />
		<?php if (! empty($description)) : ?>
			<p class="description"><?php echo esc_html($description); ?></p>
		<?php endif; ?>
	<?php
	}

	/**
	 * Render select field.
	 *
	 * @param array $args Field args.
	 * @return void
	 */
	public function render_select_field($args)
	{
		$key         = isset($args['key']) ? $args['key'] : '';
		$options     = isset($args['options']) && is_array($args['options']) ? $args['options'] : array();
		$description = isset($args['description']) ? $args['description'] : '';
		$value       = $this->get_setting($key, '');

	?>
		<select
			id="<?php echo esc_attr($this->field_id($key)); ?>"
			name="<?php echo esc_attr($this->field_name($key)); ?>">
			<?php foreach ($options as $option_value => $option_label) : ?>
				<option value="<?php echo esc_attr($option_value); ?>" <?php selected($value, $option_value); ?>>
					<?php echo esc_html($option_label); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php if (! empty($description)) : ?>
			<p class="description"><?php echo esc_html($description); ?></p>
		<?php endif; ?>
<?php
	}

	/**
	 * Build field name.
	 *
	 * @param string $key Setting key.
	 * @return string
	 */
	private function field_name($key)
	{
		return $this->option_name . '[' . $key . ']';
	}

	/**
	 * Build field ID.
	 *
	 * @param string $key Setting key.
	 * @return string
	 */
	private function field_id($key)
	{
		return 'aiok_' . sanitize_key($key);
	}
}
