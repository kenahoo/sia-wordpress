<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Styles {

  public static function init(){
    add_Action('wp_enqueue_scripts', [static::class, 'enqueueThemeSupport']);
    add_Action('wp_enqueue_scripts', [static::class, 'enqueueDefaultStyle']);
    add_Action('admin_enqueue_scripts', [static::class, 'registerDashboardStyles']);
  }

  public static function enqueueThemeSupport(){
    $arr_themes = [
      (object) [ 'name' => 'twentyten', 'check_function' => 'twentyten_setup' ],
      (object) [ 'name' => 'twentyeleven', 'check_function' => 'twentyeleven_setup' ],
      (object) [ 'name' => 'twentytwelve', 'check_function' => 'twentytwelve_setup' ],
      (object) [ 'name' => 'twentythirteen', 'check_function' => 'twentythirteen_setup' ],
      (object) [ 'name' => 'twentyfourteen', 'check_function' => 'twentyfourteen_setup' ],
      (object) [ 'name' => 'twentyfifteen', 'check_function' => 'twentyfifteen_setup' ],
      (object) [ 'name' => 'twentysixteen', 'check_function' => 'twentysixteen_setup' ],
      (object) [ 'name' => 'twentyseventeen', 'check_function' => 'twentyseventeen_setup' ],
      (object) [ 'name' => 'twentynineteen', 'check_function' => 'twentynineteen_setup' ],
      (object) [ 'name' => 'twentytwenty', 'check_function' => 'twentytwenty_theme_support' ]
    ];

    foreach ($arr_themes as $theme){
      if (function_exists($theme->check_function)){
        WP_Enqueue_Style("encyclopedia-{$theme->name}", Core::$base_url."/assets/css/themes/{$theme->name}.css");
        break;
      }
    }
  }

  public static function enqueueDefaultStyle(){
    if (Options::get('embed_default_style'))
      WP_Enqueue_Style('encyclopedia', Core::$base_url.'/assets/css/encyclopedia.css');
  }

  public static function registerDashboardStyles(){
    WP_Enqueue_Style('encyclopedia-dashboard-extension', Core::$base_url.'/assets/css/dashboard.css');
  }

}

Styles::init();
