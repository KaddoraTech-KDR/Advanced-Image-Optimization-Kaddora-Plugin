<?php
if (!defined('ABSPATH')) {
  exit;
}

$log_file = AIOK_LOGS_PATH . 'optimization.log';
$logs = [];

if (file_exists($log_file)) {

  $lines = file($log_file);
  $lines = array_reverse($lines); // newest first

  foreach ($lines as $line) {
    $logs[] = json_decode($line, true);
  }
}
?>

<div class="wrap aiok-dashboard">

  <h1 class="wp-heading-inline">Optimization Logs</h1>

  <div class="aiok-card logs-container">

    <!-- clear button -->
    <div class="aiok-log-actions">
      <strong>Recent Optimization Activity</strong>
      <button id="aiok-clear-logs" class="button button-secondary">
        Clear Logs
      </button>
    </div>

    <table class="widefat fixed striped aiok-table">

      <thead>
        <tr>
          <th>Time</th>
          <th>Image</th>
          <th>Saved</th>
          <th>Type</th>
        </tr>
      </thead>

      <tbody>

        <?php if (!empty($logs)) : ?>

          <?php foreach ($logs as $log) : ?>

            <tr>

              <td><?php echo esc_html($log['time'] ?? '-'); ?></td>

              <td>
                <?php
                $file = $log['data']['file'] ?? '';
                echo esc_html(basename($file));
                ?>
              </td>

              <td>
                <?php
                $saved = $log['data']['saved'] ?? 0;
                echo size_format($saved);
                ?>
              </td>

              <td><?php echo esc_html($log['type'] ?? 'optimization'); ?></td>

            </tr>

          <?php endforeach; ?>

        <?php else : ?>

          <tr>
            <td colspan="4">No logs found.</td>
          </tr>

        <?php endif; ?>

      </tbody>

    </table>

  </div>

</div>