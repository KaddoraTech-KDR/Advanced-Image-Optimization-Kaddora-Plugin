<?php
if (!defined('ABSPATH')) {
  exit;
}
?>

<div class="wrap aiok-dashboard">

  <h1>Image Optimization Settings</h1>

  <p class="aiok-settings-desc">
    Configure how images are optimized, compressed and delivered.
  </p>

  <form method="post" action="options.php">

    <?php settings_fields('aiok_settings_group'); ?>

    <!-- GENERAL -->
    <div class="aiok-card aiok-settings-box">
      <h2>General Settings</h2>
      <table class="form-table">
        <?php do_settings_fields('aiok-settings', 'aiok_general_section'); ?>
      </table>
    </div>

    <!-- COMPRESSION -->
    <div class="aiok-card aiok-settings-box">
      <h2>Compression Settings</h2>
      <table class="form-table">
        <?php do_settings_fields('aiok-settings', 'aiok_compression_section'); ?>
      </table>
    </div>

    <!-- FORMATS -->
    <div class="aiok-card aiok-settings-box">
      <h2>WebP & AVIF</h2>
      <table class="form-table">
        <?php do_settings_fields('aiok-settings', 'aiok_format_section'); ?>
      </table>
    </div>

    <!-- DELIVERY -->
    <div class="aiok-card aiok-settings-box">
      <h2>Delivery Settings</h2>
      <table class="form-table">
        <?php do_settings_fields('aiok-settings', 'aiok_delivery_section'); ?>
      </table>
    </div>

    <div class="aiok-save">
      <?php submit_button('Save Settings'); ?>
    </div>

  </form>

</div>