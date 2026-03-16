<?php

if (!defined('ABSPATH')) {
  exit;
}

class AIOK_LazyLoad
{

  public function init()
  {
    add_filter(
      'wp_get_attachment_image_attributes',
      array($this, 'add_lazy_loading'),
      10,
      2
    );
  }

  public function add_lazy_loading($attr, $attachment)
  {

    $settings = get_option('aiok_settings');

    if (empty($settings['lazyload_enabled'])) {
      return $attr;
    }

    if (!isset($attr['loading'])) {
      $attr['loading'] = 'lazy';
    }

    return $attr;
  }
}
