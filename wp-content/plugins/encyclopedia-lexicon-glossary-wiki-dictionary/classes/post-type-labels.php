<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Post_Type_Labels {

  public static function getEncyclopediaType(){
    $type = Options::get('encyclopedia_type');
    $type = WPML::t($type, 'Encyclopedia type');
    return $type;
  }

  public static function getArchiveSlug(){
    $slug = I18n::_x('encyclopedia', 'URL slug');
    return $slug;
  }

  public static function getItemSlug(){
    $slug = I18n::_x('encyclopedia', 'URL slug');
    return $slug;
  }

  public static function getItemSingularName(){
    $name = Options::get('item_singular_name');
    $name = WPML::t($name, 'Item singular name');
    return $name;
  }

  public static function getItemPluralName(){
    $name = Options::get('item_plural_name');
    $name = WPML::t($name, 'Item plural name');
    return $name;
  }

}
