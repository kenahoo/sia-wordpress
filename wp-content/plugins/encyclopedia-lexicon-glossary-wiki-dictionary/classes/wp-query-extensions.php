<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class WP_Query_Extensions {

  static function init(){
    add_Filter('query_vars', [static::class, 'registerQueryVars']);
    add_Action('pre_get_posts', [static::class, 'filterQuery']);
    add_Filter('posts_where', [static::class, 'filterPostsWhere'], 10, 2);
    add_Filter('posts_fields', [static::class, 'filterPostsFields'], 10, 2);
    add_Filter('posts_orderby', [static::class, 'filterPostsOrderBy'], 10, 2);
  }

  static function registerQueryVars($query_vars){
    $query_vars[] = 'prefix'; # Will store the the filter of the prefix search
    return $query_vars;
  }

  static function filterQuery($query){
		if (!$query->get('suppress_filters') && !$query->is_Feed() && (Core::isEncyclopediaArchive($query) || Core::isEncyclopediaSearch($query))){
      # Order the terms in the backend by title, ASC.
      if (!$query->get('order')) $query->set('order', 'asc');
      if (!$query->get('orderby')) $query->set('orderby', 'title');

      if ($query->is_Main_Query()){
        # Take a look at the prefix filter
        if (!$query->get('post_title_like') && get_Query_Var('prefix'))
          $query->set('post_title_like', RawUrlDecode(get_Query_Var('prefix')) . '%');

        # Change the number of terms per page
        if (!$query->get('posts_per_page'))
          $query->set('posts_per_page', Options::get('items_per_page'));
      }
		}
	}

	static function filterPostsWhere($where, $query){
		global $wpdb;

		$post_title_like = $query->get('post_title_like');
		if (!empty($post_title_like)){
      $post_title_like_esced = esc_SQL($post_title_like);
      $where .= " AND {$wpdb->posts}.post_title LIKE \"{$post_title_like_esced}\" ";
    }

    return $where;
	}

  static function filterPostsFields($fields, $query){
    global $wpdb;

    if ($query->get('orderby') == 'post_title_length')
      $fields .= ", CHAR_LENGTH({$wpdb->posts}.post_title) post_title_length";

    return $fields;
  }

  static function filterPostsOrderBy($orderby, $query){
    if ($query->get('orderby') == 'post_title_length')
      $orderby = trim(sprintf('post_title_length %s', $query->get('order')));

    return $orderby;
  }

}

WP_Query_Extensions::init();
