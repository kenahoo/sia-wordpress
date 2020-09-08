<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Template {

  static function init(){
    add_Filter('search_template', [static::class, 'changeSearchTemplate']);
  }

  static function changeSearchTemplate($template){
    global $wp_query;

    if (Core::isEncyclopediaSearch($wp_query) && $search_template = locate_Template(sprintf('search-%s.php', Post_Type::post_type_name)))
      return $search_template;
    else
      return $template;
  }

  static function load($template_name, $vars = []){
		extract($vars);
		$template_path = locate_Template($template_name);

    OB_Start();
		if (!empty($template_path))
      include $template_path;
		else
      include sprintf('%s/templates/%s', Core::$plugin_folder, $template_name);

    return OB_Get_Clean();
  }

}

Template::init();
