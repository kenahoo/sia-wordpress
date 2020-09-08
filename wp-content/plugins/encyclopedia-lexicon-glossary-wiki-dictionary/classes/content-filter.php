<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Content_Filter {

  public static function init(){
    add_Filter('the_content', [static::class, 'addRelatedItems']);
    add_Action('plugins_loaded', [static::class, 'registerContentFilter']);
  }

  public static function registerContentFilter(){
    $cross_linker_priority = Options::get('cross_linker_priority') == 'before_shortcodes' ? 10.5 : 15;
    add_Filter('the_content', [static::class, 'addCrossLinks'], $cross_linker_priority);
    add_Filter('bbp_get_forum_content', [static::class, 'addCrossLinks'], $cross_linker_priority);
    add_Filter('bbp_get_topic_content', [static::class, 'addCrossLinks'], $cross_linker_priority);
    add_Filter('bbp_get_reply_content', [static::class, 'addCrossLinks'], $cross_linker_priority);
    add_Filter('widget_text', [Core::class, 'addCrossLinks']);
  }

  public static function addRelatedItems($content){
		global $post;

    # If this is outside the loop we leave
    if (empty($post->ID) || empty($post->post_type)) return $content;

		if ($post->post_type == Post_Type::post_type_name && is_Single($post->ID)){
      if (!has_Shortcode($content, 'encyclopedia_related_items') && Options::get('related_items') != 'none' && !post_password_required()){
        $attributes = ['max_items' => Options::get('number_of_related_items')];

        if (Options::get('related_items') == 'above')
          $content = Shortcodes::Related_Items($attributes) . $content;
        else
          $content .= Shortcodes::Related_Items($attributes);
      }
		}

    return $content;
	}

  public static function addCrossLinks($content){
    global $post;

    # If this is outside the loop we leave
    if (empty($post->post_type))
      return $content;

    # If this is for the excerpt we leave
    if (doing_Filter('get_the_excerpt'))
      return $content;

    # Check if Cross-Linking is activated for this post
    if (apply_Filters('encyclopedia_link_items_in_post', True, $post)){
      $content = Core::addCrossLinks($content, $post);
    }

    return $content;
  }

}

Content_Filter::init();
