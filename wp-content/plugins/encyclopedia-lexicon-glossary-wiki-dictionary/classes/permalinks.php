<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Permalinks {
  public static
    $rewrite_rules = []; # Array with the new additional rewrite rules

  static function init(){
    add_Filter('rewrite_rules_array', [static::class, 'addRewriteRules'], 50);
    add_Action('admin_init', [static::class, 'flushRewriteRules'], 100);
  }

  static function defineRewriteRules(){
    $post_type = get_Post_Type_Object(Post_Type::post_type_name);

    # Add filter permalink structure for post type archive
    if ($post_type->has_archive){
      $archive_url_path = (True === $post_type->has_archive) ? $post_type->rewrite['slug'] : $post_type->has_archive;
      static::$rewrite_rules[ltrim(sprintf('%s/prefix:([^/]+)/?$', $archive_url_path), '/')] = sprintf('index.php?post_type=%s&prefix=$matches[1]', Post_Type::post_type_name);
      static::$rewrite_rules[ltrim(sprintf('%s/prefix:([^/]+)/page/([0-9]{1,})/?$', $archive_url_path), '/')] = sprintf('index.php?post_type=%s&prefix=$matches[1]&paged=$matches[2]', Post_Type::post_type_name);
    }

    # Add filter permalink structure for taxonomy archives
    if ($taxonomies = Post_Type::getAssociatedTaxonomies()){
      foreach ($taxonomies as $taxonomy){
        $taxonomy_slug = $taxonomy->rewrite['slug'];
        static::$rewrite_rules[ltrim(sprintf('%s/([^/]+)/prefix:([^/]+)/?$', $taxonomy_slug), '/')] = sprintf('index.php?%s=$matches[1]&prefix=$matches[2]', $taxonomy->name);
        static::$rewrite_rules[ltrim(sprintf('%s/([^/]+)/prefix:([^/]+)/page/([0-9]{1,})/?$', $taxonomy_slug), '/')] = sprintf('index.php?%s=$matches[1]&prefix=$matches[2]&paged=$matches[3]', $taxonomy->name);
      }
    }
  }

  static function addRewriteRules($current_rewrite_rules){
    setType($current_rewrite_rules, 'ARRAY');
    $current_rewrite_rules = Array_Filter($current_rewrite_rules);
    if (empty($current_rewrite_rules)) return $current_rewrite_rules;

    if (empty(static::$rewrite_rules)) static::defineRewriteRules();
    $arr_rules = Array_Merge(static::$rewrite_rules, $current_rewrite_rules);

    return $arr_rules;
  }

  static function flushRewriteRules(){
    $current_rewrite_rules = get_Option('rewrite_rules');
    setType($current_rewrite_rules, 'ARRAY');
    if (empty(static::$rewrite_rules))
      static::defineRewriteRules();

    foreach (static::$rewrite_rules as $rewrite_rule => $redirect){
      if (empty($current_rewrite_rules[$rewrite_rule])){
        flush_Rewrite_Rules();
        return;
      }
    }
  }

}

Permalinks::init();
