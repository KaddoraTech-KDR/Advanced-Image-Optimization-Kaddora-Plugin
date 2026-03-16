<?php

if (!defined('ABSPATH')) {
  exit;
}

class AIOK_Bulk_Optimizer
{

  public function init()
  {
    add_action('wp_ajax_aiok_bulk_optimize', array($this, 'bulk_optimize'));
  }

  public function bulk_optimize()
  {

    check_ajax_referer('aiok_bulk_optimize_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
      wp_send_json_error('Permission denied');
    }

    $attachments = get_posts(array(
      'post_type' => 'attachment',
      'post_mime_type' => 'image',
      'posts_per_page' => 5,
      'post_status' => 'inherit',
      'meta_query' => array(
        array(
          'key' => '_aiok_optimized',
          'compare' => 'NOT EXISTS'
        )
      )
    ));

    if (!$attachments) {

      wp_send_json_success(array(
        'optimized' => 0,
        'total' => 0,
        'saved' => 0
      ));
    }

    $optimized = 0;
    $total_saved = 0;

    $optimizer = new AIOK_Optimizer();

    foreach ($attachments as $attachment) {

      $file = get_attached_file($attachment->ID);

      if (!$file || !file_exists($file)) {
        continue;
      }

      if (get_post_meta($attachment->ID, '_aiok_optimized', true)) {
        continue;
      }

      $optimizer = new AIOK_Optimizer();

      $saved = $optimizer->optimize($file);

      if ($saved > 0) {
        $total_saved += $saved;
      }

      update_post_meta($attachment->ID, '_aiok_optimized', 1);

      $optimized++;
    }

    wp_send_json_success(array(
      'optimized' => $optimized,
      'total' => count($attachments),
      'saved' => size_format($total_saved)
    ));
  }
}
