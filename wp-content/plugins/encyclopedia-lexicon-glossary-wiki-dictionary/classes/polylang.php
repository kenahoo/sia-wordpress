<?php Namespace WordPress\Plugin\Encyclopedia;

use WP_Query;

abstract class Polylang {

  public static function init(){
    add_Filter('gettext_with_context', [static::class, 'filterGettextWithContext'], 1, 4);
    add_Filter('encyclopedia_available_prefix_filters', [static::class, 'filterAvailablePrefixFilters']);
  }

  public static function isActive(){
    return defined('POLYLANG_VERSION');
  }

  public static function filterGettextWithContext($translation, $text, $context, $domain){
    # The post type slug MUST NOT be translated! You need to translate your slug in the translation plugin directly
    if (static::isActive() && $context == 'URL slug' && $domain == I18n::getTextDomain())
      return $text;
    else
      return $translation;
  }

  public static function filterAvailablePrefixFilters($arr_filter){
    if (static::isActive() && is_Array($arr_filter)){
      foreach ($arr_filter as $index => $filter){
        # Check if there are posts behind this filter in this language
        $query = new WP_Query([
          'post_type' => Post_Type::post_type_name,
          'post_title_like' => $filter->prefix . '%',
          'posts_per_page' => 1,
          'cache_results' => False,
          'no_count_rows' => True
        ]);

        if (!$query->have_Posts())
          unset($arr_filter[$index]);
      }

      $arr_filter = Array_Values($arr_filter);
    }

    return $arr_filter;
  }

}

Polylang::init();
