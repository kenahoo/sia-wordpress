<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Mocking_Bird {
  public static
    $banner_id;

  public static function init(){
    add_Action('admin_menu', [static::class, 'addProLink'], 20);
    add_Action('registered_post_type', [static::class, 'changePostTypeLabels'], 10, 2);
    add_Action('all_admin_notices', [static::class, 'printProBanner']);
    add_Action('admin_print_footer_scripts', [static::class, 'moveProBannerInsideBlockEditor'], 20);
  }

  public static function getProNotice($message_id = 'option'){
    $author_link = I18n::_x('https://dennishoppe.de/en/wordpress-plugins/encyclopedia', 'Link to the authors website');

    $arr_message = [
      'upgrade' => I18n::__('Upgrade to Pro'),
      'upgrade_url' => $author_link,
      'feature' => sprintf(I18n::__('Available in the <a href="%s" target="_blank">Pro Version</a> only.'), $author_link),
      'unlock' => sprintf('<a href="%1$s" title="%2$s" class="upgrade-encyclopedia" target="_blank"><span class="dashicons dashicons-lock"></span><span class="dashicons dashicons-unlock onhover"></span> <span class="onhover">%3$s</span></a>', $author_link, I18n::__('Unlock this feature'), I18n::__('Upgrade to Pro')),
      'option' => sprintf(I18n::__('This option is changeable in the <a href="%s" target="_blank">Pro Version</a> only.'), $author_link),
      'count_limit' => sprintf(I18n::__('In the <a href="%1$s" target="_blank">Pro Version of Encyclopedia</a> you will take advantage of unlimited %2$s and many more features.'), $author_link, Post_Type_Labels::getItemPluralName()),
    ];

    if (empty($arr_message[$message_id]))
      return False;
    else
      return $arr_message[$message_id];
  }

  public static function printProNotice($message_id = 'option'){
    echo static::getProNotice($message_id);
  }

  public static function countItems(){
    static $count = -1;
    if ($count < 0){
      $count_items = (array) WP_Count_Posts(Post_Type::post_type_name);
      $count_items = Array_Merge([
        'publish' => 0,
        'future' => 0,
        'pending' => 0,
        'draft' => 0,
        'private' => 0,
      ], $count_items);
      $count_items = Array_Map('intval', $count_items);
      $count = $count_items['publish'] + $count_items['future'] + $count_items['pending'] + $count_items['draft'] + $count_items['private'];
    }
    return $count;
  }

  public static function addProLink(){
    if (static::countItems() >= 2){
      $menu_label = '<span style="color:#46b450;font-weight:bold;text-transform:uppercase">'.static::getProNotice('upgrade').'</span>';
      add_SubMenu_Page('edit.php?post_type='.Post_Type::post_type_name, Null, $menu_label, 'edit_posts', static::getProNotice('upgrade_url'));
    }
  }

  public static function changePostTypeLabels($post_type, $post_type_obj){
    if ($post_type == Post_Type::post_type_name && is_Admin() && static::countItems() >= 5){
      $suffix = sprintf(' (%s)', I18n::__('Free Version'));
      $post_type_obj->labels->name .= $suffix;
      $post_type_obj->labels->menu_name .= $suffix;
    }
  }

  public static function printProBanner(){
    global $current_screen;
    static::$banner_id = uniqid();

    if ($current_screen->base == 'settings_page_encyclopedia-options' || ($current_screen->post_type == Post_Type::post_type_name && static::countItems() >= 8)): ?>
    <div id="<?php echo static::$banner_id ?>" style="margin-top:20px;position:relative;width:90%;max-width:726px;display:block;clear:both;border:2px solid #46b450;">
      <a href="<?php static::printProNotice('upgrade_url') ?>" title="<?php static::printProNotice('upgrade') ?>" target="_blank" style="text-decoration:none">
        <img src="<?php echo Core::$base_url ?>/assets/img/plugin-logo-1544x500.png" alt="" width="1544" height="500" style="width:100%;height:auto;display:block;margin:0;padding:0">
        <span style="position:absolute;top:0;right:0;background:#D3D3D3;border-radius:0 0 0 5px;color:black;padding:3px 5px;display:inline-block"><?php echo I18n::__('Enjoy all features of the Pro Version') ?></span>
        <span style="position:absolute;bottom:0;left:0;background:#46b450;border-radius:0 5px 0 0;color:white;padding:3px 5px;display:inline-block"><?php echo I18n::__('If you like the free version, you will love <u><strong>Encyclopedia Pro</strong></u>!') ?></span>
      </a>
    </div>
    <?php endif;
  }

  public static function moveProBannerInsideBlockEditor($post_type){
    global $current_screen;
    if (static::$banner_id && $current_screen->base == 'post' && $current_screen->post_type == Post_Type::post_type_name): ?>
    <script type="text/javascript">
      (function($){
        var
          $body = $('body'),
          block_editor_active = $body.hasClass('block-editor-page'),
          banner_id = '<?php echo static::$banner_id ?>',
          $banner = $('div#' + banner_id + ':first'),
          moveProBanner = function(){
            var $editor_part = $('div.editor-post-title:first');
            if ($editor_part.length){
              stopSearchTimer();
              $banner
                .insertBefore($editor_part)
                .css({
                  margin: '0 auto',
                  fontSize: '14px'
                });
            }
          },
          searchTimer = block_editor_active ? window.setInterval(moveProBanner, 333) : false,
          stopSearchTimer = function(){ window.clearInterval(searchTimer); },
          emergencyStopTimer = searchTimer ? window.setTimeout(stopSearchTimer, 10000) : false;
      }(jQuery));
    </script>
    <?php endif;
  }

}

Mocking_Bird::init();
