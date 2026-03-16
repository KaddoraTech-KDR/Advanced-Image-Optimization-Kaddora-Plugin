<?php

if (!defined('ABSPATH')) {
  exit;
}

class AIOK_Optimizer
{
  /**
   * Optimize image file
   */
  public function optimize($file)
  {

    if (!file_exists($file)) {
      return false;
    }

    /* Backup original image */
    $this->backup_original($file);

    $original_size = filesize($file);

    $mime = mime_content_type($file);

    if ($mime === 'image/jpeg') {
      $this->optimize_jpeg($file);
    }

    if ($mime === 'image/png') {
      $this->optimize_png($file);
    }

    $new_size = filesize($file);

    return $original_size - $new_size;
  }

  /**
   * Optimize JPEG image
   */
  private function optimize_jpeg($file)
  {

    $settings = get_option('aiok_settings');

    $quality = isset($settings['jpeg_quality'])
      ? (int) $settings['jpeg_quality']
      : 82;

    $original_size = filesize($file);

    $image = imagecreatefromjpeg($file);

    if (!$image) {
      return false;
    }

    imagejpeg($image, $file, $quality);

    imagedestroy($image);

    $new_size = filesize($file);

    $this->log_result($file, $original_size, $new_size);

    /* WebP + AVIF conversion */
    if (class_exists('AIOK_Converter')) {

      $converter = new AIOK_Converter();
      $converter->convert_to_webp($file);
      $converter->convert_to_avif($file);
    }

    return true;
  }

  /**
   * Optimize PNG image
   */
  private function optimize_png($file)
  {

    $settings = get_option('aiok_settings');

    $quality = isset($settings['png_quality'])
      ? (int) $settings['png_quality']
      : 82;

    $compression = 9 - floor(($quality / 100) * 9);

    $original_size = filesize($file);

    $image = imagecreatefrompng($file);

    if (!$image) {
      return false;
    }

    imagepng($image, $file, $compression);

    imagedestroy($image);

    // $new_size = filesize($file);

    // $this->log_result($file, $original_size, $new_size);

    $new_size = filesize($file);

    if ($new_size >= $original_size) {
      return 0;
    }

    $this->log_result($file, $original_size, $new_size);

    /* WebP conversion */
    if (class_exists('AIOK_Converter')) {

      $converter = new AIOK_Converter();

      $converter->convert_to_webp($file);
      $converter->convert_to_avif($file);
    }

    return true;
  }

  /**
   * Log optimization result
   */
  private function log_result($file, $original, $new)
  {

    if (!class_exists('AIOK_Logger')) {
      return;
    }

    // $saved = $original - $new;
    $webp = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file);
    $avif = preg_replace('/\.(jpg|jpeg|png)$/i', '.avif', $file);

    $best_size = $new;

    if (file_exists($webp)) {
      $webp_size = filesize($webp);
      if ($webp_size < $best_size) {
        $best_size = $webp_size;
      }
    }

    if (file_exists($avif)) {
      $avif_size = filesize($avif);
      if ($avif_size < $best_size) {
        $best_size = $avif_size;
      }
    }

    $saved = $original - $best_size;

    $data = array(
      'file' => $file,
      'original_size' => $original,
      'new_size' => $new,
      'saved' => $saved,
    );

    static $logger = null;

    if (!$logger) {
      $logger = new AIOK_Logger();
    }

    $logger->log('optimization', $data);
  }

  // backup_original
  private function backup_original($file)
  {

    $settings = get_option('aiok_settings');

    if (empty($settings['backup_original'])) {
      return;
    }

    $backup_path = AIOK_BACKUPS_PATH . basename($file);

    if (!file_exists($backup_path)) {

      copy($file, $backup_path);
    }
  }
}
