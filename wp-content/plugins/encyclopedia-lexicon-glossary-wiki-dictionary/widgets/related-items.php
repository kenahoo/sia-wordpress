<?php Namespace WordPress\Plugin\Encyclopedia;

use WP_Widget;

class Related_Items_Widget extends WP_Widget {

  function __construct(){
    # Setup the Widget data
    parent::__construct(
      'encyclopdia_related_items',
      sprintf(I18n::__('%s: Related %s'), Post_Type_Labels::getEncyclopediaType(), Post_Type_Labels::getItemPluralName()),
      ['description' => sprintf(I18n::__('A list with the related %s.'), Post_Type_Labels::getItemSingularName())]
    );
  }

  static function registerWidget(){
    if (doing_Action('widgets_init'))
      register_Widget(static::class);
    else
      add_Action('widgets_init', [static::class, __FUNCTION__]);
  }

  function getDefaultOptions(){
    # Default settings
    return [
      'title' => '',
      'number'  => 5
    ];
  }

  function loadOptions(&$options){
    setType($options, 'ARRAY');
    $options = Array_Filter($options);
    $options = Array_Merge($this->getDefaultOptions(), $options);
    setType($options, 'OBJECT');
  }

  function Form($options){
    $this->loadOptions($options);
    ?>

    <p>
      <label for="<?php echo $this->get_Field_Id('title') ?>"><?php I18n::_e('Title:') ?></label>
      <input type="text" id="<?php echo $this->get_Field_Id('title') ?>" name="<?php echo $this->get_Field_Name('title')?>" value="<?php echo esc_Attr($options->title) ?>" class="widefat">
    </p>

    <p>
      <label for="<?php echo $this->get_Field_Id('number') ?>"><?php I18n::_e('Number:') ?></label>
      <input type="number" id="<?php echo $this->get_Field_Id('number') ?>" name="<?php echo $this->get_Field_Name('number')?>" value="<?php echo esc_Attr($options->number) ?>" min="1" max="<?php echo PHP_INT_MAX ?>" step="1" class="widefat">
      <small><?php printf(I18n::__('The number of %s the widget should show.'), Post_Type_Labels::getItemPluralName()) ?></small>
    </p>

    <?php
  }

  function Widget($widget, $options){
    if (!is_Singular()) return False;

    # Load widget args
    setType($widget, 'OBJECT');

    # Load options
    $this->loadOptions($options);

    # Load widget title
    $widget->title = apply_Filters('widget_title', $options->title, (array) $options, $this->id_base);

    # Load the related terms
    $widget->items = Post_Relations::getTermRelatedItems([
      'number' => $options->number
    ]);

    if (!$widget->items) return;

    # Display Widget
    echo $widget->before_widget;
    !empty($widget->title) && print($widget->before_title . $widget->title . $widget->after_title);
    echo Template::load('encyclopedia-related-items-widget.php', ['widget' => $widget, 'options' => $options]);
    echo $widget->after_widget;

    # Reset Post data
    WP_Reset_Postdata();
  }

}

Related_Items_Widget::registerWidget();
