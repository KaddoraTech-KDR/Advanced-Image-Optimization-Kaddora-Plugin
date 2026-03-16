<?php

if (!defined('ABSPATH')) {
  exit;
}

class AIOK_Media
{
  /**
   * Register hooks
   */
  public function init()
  {
    add_filter(
      'wp_generate_attachment_metadata',
      array($this, 'handle_image_upload'),
      10,
      2
    );
  }

  // handle_image_upload
  public function handle_image_upload($metadata, $attachment_id)
  {

    $file = get_attached_file($attachment_id);

    if (!$file || !file_exists($file)) {
      return $metadata;
    }

    $mime = get_post_mime_type($attachment_id);

    if (!in_array($mime, array('image/jpeg', 'image/png'))) {
      return $metadata;
    }

    if (class_exists('AIOK_Optimizer')) {

      $optimizer = new AIOK_Optimizer();
      $optimizer->optimize($file);
    }

    return $metadata;
  }
}
