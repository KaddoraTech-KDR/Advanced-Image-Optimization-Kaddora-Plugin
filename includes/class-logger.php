<?php

if (!defined('ABSPATH')) {
  exit;
}

class AIOK_Logger
{

  private $log_file;

  public function __construct()
  {
    $this->log_file = AIOK_LOGS_PATH . 'optimization.log';
    add_action('wp_ajax_aiok_clear_logs', [$this, 'clear_logs']);
  }

  // clear_logs
  public function clear_logs()
  {
    check_ajax_referer('aiok_admin_nonce', 'nonce');

    if (file_exists($this->log_file)) {
      file_put_contents($this->log_file, '');
    }

    wp_send_json_success();
  }

  // log
  public function log($type, $data)
  {

    $settings = get_option('aiok_settings');

    if (empty($settings['keep_logs'])) {
      return;
    }

    $entry = array(
      'time' => current_time('mysql'),
      'type' => $type,
      'data' => $data
    );

    $line = json_encode($entry) . PHP_EOL;

    file_put_contents($this->log_file, $line, FILE_APPEND);
  }
}
