<?php

if (!defined('ABSPATH')) {
  exit;
}

class AIOK_CDN
{

  public function init()
  {
    add_filter(
      'wp_get_attachment_url',
      array($this, 'rewrite_cdn_url'),
      10,
      2
    );
  }

  public function rewrite_cdn_url($url, $attachment_id)
  {

    $settings = get_option('aiok_settings');

    if (empty($settings['cdn_enabled'])) {
      return $url;
    }

    if (empty($settings['cdn_url'])) {
      return $url;
    }

    $upload_dir = wp_get_upload_dir();

    $base_url = $upload_dir['baseurl'];
    $cdn_url  = rtrim($settings['cdn_url'], '/');

    $new_url = str_replace($base_url, $cdn_url, $url);

    return $new_url;
  }
}
