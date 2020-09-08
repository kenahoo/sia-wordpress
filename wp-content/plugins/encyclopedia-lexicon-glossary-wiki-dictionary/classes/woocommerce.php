<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Woocommerce {

  public static function init(){
    add_Filter('woocommerce_attribute', [Content_Filter::class, 'addCrossLinks']);
  }

}

Woocommerce::init();
