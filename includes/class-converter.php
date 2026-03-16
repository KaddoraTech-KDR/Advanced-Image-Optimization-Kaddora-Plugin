<?php

if (!defined('ABSPATH')) {
  exit;
}

class AIOK_Converter
{
  // convert_to_avif
  public function convert_to_avif($file)
  {

    // error_log('AVIF conversion started for: ' . $file);

    if (!function_exists('imageavif')) {
      return false;
    }

    $settings = get_option('aiok_settings');

    if (empty($settings['generate_avif'])) {
      return false;
    }

    // $mime = mime_content_type($file);
    $mime = wp_check_filetype($file)['type'];

    $avif_file = preg_replace('/\.(jpg|jpeg|png)$/i', '.avif', $file);

    if ($mime === 'image/jpeg') {

      $image = imagecreatefromjpeg($file);
    } elseif ($mime === 'image/png') {

      $image = imagecreatefrompng($file);
    } else {

      return false;
    }

    imageavif($image, $avif_file, 50);

    imagedestroy($image);

    return true;
  }

  /**
   * Convert image to WebP
   */
  public function convert_to_webp($file)
  {

    if (!file_exists($file)) {
      return false;
    }

    $settings = get_option('aiok_settings');

    if (empty($settings['generate_webp'])) {
      return false;
    }

    $mime = mime_content_type($file);

    $webp_file = $this->get_webp_path($file);

    if ($mime === 'image/jpeg') {
      return $this->jpeg_to_webp($file, $webp_file);
    }

    if ($mime === 'image/png') {
      return $this->png_to_webp($file, $webp_file);
    }

    return false;
  }

  /**
   * JPEG to WebP
   */
  private function jpeg_to_webp($file, $webp_file)
  {

    $image = imagecreatefromjpeg($file);

    if (!$image) {
      return false;
    }

    imagewebp($image, $webp_file, 82);

    imagedestroy($image);

    return true;
  }

  /**
   * PNG to WebP
   */
  private function png_to_webp($file, $webp_file)
  {

    $image = imagecreatefrompng($file);

    if (!$image) {
      return false;
    }

    imagepalettetotruecolor($image);
    imagealphablending($image, true);
    imagesavealpha($image, true);

    imagewebp($image, $webp_file, 82);

    imagedestroy($image);

    return true;
  }

  /**
   * Get WebP file path
   */
  private function get_webp_path($file)
  {
    return preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file);
  }
}
