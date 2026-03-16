<?php

$log_file = AIOK_LOGS_PATH . 'optimization.log';

$total_images = 0;
$total_saved = 0;

if (file_exists($log_file)) {

  $lines = file($log_file);

  foreach ($lines as $line) {

    $entry = json_decode($line, true);

    if (isset($entry['data']['saved'])) {

      $total_images++;
      $total_saved += $entry['data']['saved'];
    }
  }
}

$images = get_posts(array(
  'post_type' => 'attachment',
  'post_mime_type' => 'image',
  'posts_per_page' => -1
));

?>

<div class="wrap aiok-dashboard">

  <h1>Image Optimization Dashboard</h1>

  <div class="aiok-stats">

    <div class="aiok-card">
      <span class="dashicons dashicons-format-image"></span>
      <h2><?php echo $total_images; ?></h2>
      <p>Total Images Optimized</p>
    </div>

    <div class="aiok-card">
      <span class="dashicons dashicons-database"></span>
      <h2><?php echo size_format($total_saved); ?></h2>
      <p>Total Space Saved</p>
    </div>
    <div class="aiok-card">
      <span class="dashicons dashicons-performance"></span>
      <h2>WebP / AVIF</h2>
      <p>Modern Formats Enabled</p>
    </div>
    <div class="aiok-card">
      <span class="dashicons dashicons-images-alt2"></span>
      <h2><?php echo count($images); ?></h2>
      <p>Total Media Images</p>
    </div>
  </div>

  <div class="aiok-actions">
    <a href="<?php echo admin_url('admin.php?page=aiok-bulk-optimizer'); ?>" class="button button-primary">
      Run Bulk Optimization
    </a>
    <a href="<?php echo admin_url('admin.php?page=aiok-settings'); ?>" class="button">
      Open Settings
    </a>
  </div>


  <h2 class="aiok-recent-title">Recent Optimizations</h2>

  <table class="widefat aiok-table">

    <thead>
      <tr>
        <th>Image</th>
        <th>Saved</th>
        <th>Date</th>
      </tr>
    </thead>

    <tbody>
      <?php
      if (file_exists($log_file)) {
        $lines = array_slice(file($log_file), -5);
        foreach ($lines as $line) {
          $data = json_decode($line, true);
      ?>
          <tr>
            <td><?php echo basename($data['data']['file']); ?></td>
            <td><?php echo size_format($data['data']['saved']); ?></td>
            <td><?php echo $data['time']; ?></td>
          </tr>
      <?php }
      } ?>
    </tbody>

  </table>

</div>