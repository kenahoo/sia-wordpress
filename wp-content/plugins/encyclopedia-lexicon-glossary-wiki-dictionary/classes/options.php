<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Options {
  const
    page_slug = 'encyclopedia-options',
    options_key = 'wp_plugin_encyclopedia';

  private static
    $arr_option_box = [];

  public static function init(){
    # Option boxes
    static::$arr_option_box = [ 'main' => [], 'side' => [] ];
    add_Action('admin_menu', [static::class, 'addOptionsPage']);
  }

  public static function addOptionsPage(){
    $handle = add_Options_Page(
      sprintf(I18n::__('%s Settings'), Post_Type_Labels::getEncyclopediaType()),
      Post_Type_Labels::getEncyclopediaType(),
      'manage_options',
      static::page_slug,
      [static::class, 'printOptionsPage']
    );

    # Add options page link to post type sub menu
    add_SubMenu_Page('edit.php?post_type='.Post_Type::post_type_name, Null, I18n::__('Settings'), 'manage_options', 'options-general.php?page='.static::page_slug);

    # Add JavaScript to this handle
    add_Action('load-' . $handle, [static::class, 'loadOptionsPage']);

    # Add option boxes
    static::addOptionBox(I18n::__('Labels'), Core::$plugin_folder.'/options-page/post-type-labels.php');
    static::addOptionBox(I18n::__('Appearance'), Core::$plugin_folder.'/options-page/appearance.php');
    static::addOptionBox(I18n::__('Features'), Core::$plugin_folder.'/options-page/features.php');
    static::addOptionBox(I18n::__('Taxonomies'), Core::$plugin_folder.'/options-page/taxonomies.php');
    static::addOptionBox(I18n::__('Archives'), Core::$plugin_folder.'/options-page/archive-page.php');
    static::addOptionBox(I18n::__('Search'), Core::$plugin_folder.'/options-page/search.php');
    static::addOptionBox(I18n::__('Single View'), Core::$plugin_folder.'/options-page/single-page.php');
    static::addOptionBox(I18n::__('Cross Linking'), Core::$plugin_folder.'/options-page/cross-linking.php');
    static::addOptionBox(I18n::__('Tooltips'), Core::$plugin_folder.'/options-page/tooltips.php');
    static::addOptionBox(I18n::__('Archive URLs'), Core::$plugin_folder.'/options-page/archive-link.php', 'side');
  }

  public static function getOptionsPageUrl($parameters = []){
    $url = add_Query_Arg(['page' => static::page_slug], Admin_Url('options-general.php'));
    if (is_Array($parameters) && !empty($parameters)) $url = add_Query_Arg($parameters, $url);
    return $url;
  }

  public static function loadOptionsPage(){
    flush_Rewrite_Rules();

    # If this is a Post request to save the options
    if (static::saveOptions()){
      WP_Redirect(static::getOptionsPageUrl(['options_saved' => 'true']));
    }

    WP_Enqueue_Style('dashboard');

    WP_Enqueue_Style(static::page_slug, Core::$base_url . '/options-page/options-page.css');

    # Remove incompatible JS Libs
    WP_Dequeue_Script('post');
  }

  public static function printOptionsPage(){
    include Core::$plugin_folder.'/options-page/options-page.php';
  }

  public static function addOptionBox($title, $include_file, $column = 'main'){
    # if the box file does not exists we are wrong here
    if (!is_File($include_file)) return False;

    # Title cannot be empty
    if (empty($title)) $title = '&nbsp;';

    # Column (can be 'side' or 'main')
    if ($column != 'main') $column = 'side';

    # Add a new box
    static::$arr_option_box[$column][] = (Object) [
      'title' => $title,
      'file' => $include_file,
      'slug' => PathInfo($include_file, PATHINFO_FILENAME)
    ];
  }

  public static function get($key = Null, $default = False){
    static $arr_options;

    # Read Options
    if (empty($arr_options)){
      $saved_options = get_Option(static::options_key);
      setType($saved_options, 'ARRAY');
      $default_options = static::getDefaultOptions();
      $arr_options = Array_Merge($default_options, $saved_options);
    }

    # Locate the option
    if (empty($key))
      return $arr_options;
    elseif (isSet($arr_options[$key]))
      return $arr_options[$key];
    else
      return $default;
  }

  public static function saveOptions(){
    # Check if this is a post request
    if (empty($_POST)) return False;

    # Check the nonce
    check_Admin_Referer('save_encyclopedia_options');

    # Clean the Post array
    $options = stripSlashes_Deep($_POST);
    $options = Array_Filter($options, function($value){ return $value == '0' || !empty($value); });

    # Save Options
    update_Option(static::options_key, $options);

    return True;
  }

  public static function getDefaultOptions(){
    return [
      'encyclopedia_type' => I18n::__('Encyclopedia'),
      'item_singular_name' => I18n::__('Entry'),
      'item_plural_name' => I18n::__('Entries'),
      'embed_default_style' => True,
      'enable_editor' => True,
      'enable_block_editor' => False,
      'enable_excerpt' => True,
      'encyclopedia_tags' => True,
      'enable_archive' => True,
      'items_per_page' => get_Option('posts_per_page'),
      'prefix_filter_for_archives' => True,
      'prefix_filter_archive_depth' => 3,
      'prefix_filter_for_singulars' => True,
      'prefix_filter_singular_depth' => 3,
      'min_relation_threshold' => 1,
      'cross_link_title_length' => apply_Filters('excerpt_length', 55),
      'cross_linker_priority' => 'after_shortcodes',
      'activate_tooltips' => True,
    ];
  }

}

Options::init();
