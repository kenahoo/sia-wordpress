<?php Namespace WordPress\Plugin\Encyclopedia ?>

<div class="wrap">

  <h1><?php printf(I18n::__('%s Settings'), Post_Type_Labels::getEncyclopediaType()) ?></h1>

  <?php if (isSet($_GET['options_saved'])): ?>
  <div id="message" class="updated fade">
    <p><strong><?php I18n::_e('Settings saved.') ?></strong></p>
  </div>
  <?php endif ?>

  <form method="post" action="">
    <div class="metabox-holder">

      <div class="postbox-container left">
        <?php foreach (Options::$arr_option_box['main'] as $box): ?>
          <div class="postbox">
            <h2 class="hndle"><?php echo $box->title ?></h2>
            <div class="inside"><?php include $box->file ?></div>
          </div>
        <?php endforeach ?>
      </div>

      <div class="postbox-container right">
        <?php foreach (Options::$arr_option_box['side'] as $box): ?>
          <div class="postbox">
            <h2 class="hndle"><?php echo $box->title ?></h2>
            <div class="inside"><?php include $box->file ?></div>
          </div>
        <?php endforeach ?>
      </div>

    </div>

    <p class="submit">
      <input type="submit" class="button-primary" value="<?php I18n::_e('Save Changes') ?>">
    </p>

    <?php WP_Nonce_Field('save_encyclopedia_options') ?>
  </form>

</div>
