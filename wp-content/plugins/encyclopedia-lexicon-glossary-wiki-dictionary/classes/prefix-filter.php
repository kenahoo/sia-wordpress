<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Prefix_Filter {

  public static function generate($current_prefix = '', $depth = False, $taxonomy_term = False){
		$arr_filter = []; # This will be the function result
    $active_prefix = '';

    do {
			do {
        $arr_available_filters = static::getFilters($active_prefix, $taxonomy_term);

        if (empty($arr_available_filters))
          break 2;
        elseif (count($arr_available_filters) == 1){
          if ($arr_available_filters[0]->items > 1 && $active_prefix != $arr_available_filters[0]->prefix){ # We found one prefix only
            $active_prefix = $arr_available_filters[0]->prefix;
            continue;
          }
          else { # We found only one item or the items have the same prefix as we are using at the moment ($active_prefix)
            break 2;
          }
        }
        else
          break;

      } while (count($arr_available_filters) < 2);

			$arr_filter_line = [];
      $active_prefix = '';

			foreach ($arr_available_filters as $available_filter){
        if (StriPos($current_prefix, $available_filter->prefix) === 0)
          $active_prefix = $available_filter->prefix;

        $filter = (Object) [
          'prefix' => MB_Convert_Case($available_filter->prefix, MB_CASE_TITLE),  # UCFirst for multibyte chars
          'items' => $available_filter->items, # number of available items with this prefix
          'link' => Post_Type::getArchiveLink($available_filter->prefix, $taxonomy_term),
          'active' => $active_prefix == $available_filter->prefix,
          'disabled' => False
        ];

        if (empty($filter->link))
          $filter->disabled = True;

				$arr_filter_line[$available_filter->prefix] = $filter;
			}
			$arr_filter[] = $arr_filter_line;

      # Check filter depth limit
      if ($depth && count($arr_filter) >= $depth) break;

		} while ($active_prefix);

    # Run a filter
    $arr_filter = apply_Filters('encyclopedia_prefix_filter_links', $arr_filter, $depth);

		return $arr_filter;
	}

  public static function getFilters($prefix = '', $taxonomy_term = False){
    global $wpdb;

    $prefix_length = MB_StrLen($prefix) + 1;
    $tables = [$wpdb->posts.' AS posts'];
    $where = [
      'posts.post_status  =     "publish"',
      'posts.post_type    =     "'.Post_Type::post_type_name.'"',
      'posts.post_title   !=    ""',
      'posts.post_title   LIKE  "'.$prefix.'%"'
    ];

    if ($taxonomy_term){
      $tables[] = "{$wpdb->term_relationships} AS term_relationships";
      $where[] = 'term_relationships.object_id = posts.id';
      $where[] = "term_relationships.term_taxonomy_id = {$taxonomy_term->term_taxonomy_id}";
    }

    $stmt = 'SELECT   LOWER(SUBSTRING(posts.post_title,1,'.$prefix_length.')) prefix,
                      COUNT(ID) items
             FROM     '.join(',', $tables).'
             WHERE    '.join(' AND ', $where).'
             GROUP BY prefix
             ORDER BY prefix ASC';

    $arr_filter = $wpdb->get_Results($stmt);
    $arr_filter = apply_Filters('encyclopedia_available_prefix_filters', $arr_filter, $prefix, $taxonomy_term);

    return $arr_filter;
	}

  public static function printFilter($current_filter = '', $filter_depth = False, $taxonomy_term = False){
    $prefix_filter = static::generate($current_filter, $filter_depth, $taxonomy_term);

    if (!empty($prefix_filter))
      echo Template::load('encyclopedia-prefix-filter.php', ['filter' => $prefix_filter]);
    else
      return False;
  }

}
