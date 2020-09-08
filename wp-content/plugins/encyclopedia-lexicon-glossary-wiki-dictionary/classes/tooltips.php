<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Tooltips {

  public static function init(){
    add_Action('init', [static::class, 'registerScripts']);
    add_Action('wp_enqueue_scripts', [static::class, 'enqueueScripts']);
  }

  public static function registerScripts(){
    WP_Register_Script('tooltipster', Core::$base_url.'/assets/js/tooltipster.bundle.min.js', ['jquery'], '4.2.6', True);
    WP_Register_Script('encyclopedia-tooltips', Core::$base_url.'/assets/js/tooltips.js', ['tooltipster'], Null, True);

    $js_parameters = [];
    $js_parameters = apply_Filters('encyclopedia_tooltip_js_parameters', $js_parameters);

    WP_Localize_Script('encyclopedia-tooltips', 'Encyclopedia_Tooltips', $js_parameters);
  }

  public static function enqueueScripts(){
    if (Options::get('activate_tooltips')){
      WP_Enqueue_Style('encyclopedia-tooltips', Core::$base_url.'/assets/css/tooltips.css');
      WP_Enqueue_Script('encyclopedia-tooltips');
    }
  }

}

Tooltips::init();
