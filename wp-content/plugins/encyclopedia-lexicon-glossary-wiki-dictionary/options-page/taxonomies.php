<?php Namespace WordPress\Plugin\Encyclopedia ?>

<table class="form-table">
<tr>
  <th><label><?php I18n::_e('Categories') ?></label></th>
  <td>
		<select <?php disabled(True) ?> >
			<option <?php disabled(True) ?> ><?php I18n::_e('On') ?></option>
			<option <?php selected(True) ?> ><?php I18n::_e('Off') ?></option>
		</select><?php Mocking_Bird::printProNotice('unlock') ?>
		<p class="help"><?php printf(I18n::__('Categories could help you structuring your %s.'), Post_Type_Labels::getItemPluralName()) ?></p>
	</td>
</tr>

<tr>
  <th><label for="encyclopedia_tags"><?php I18n::_e('Tags') ?></label></th>
  <td>
		<select name="encyclopedia_tags" id="encyclopedia_tags">
			<option value="1" <?php selected(Options::get('encyclopedia_tags')) ?> ><?php I18n::_e('On') ?></option>
			<option value="0" <?php selected(!Options::get('encyclopedia_tags')) ?> ><?php I18n::_e('Off') ?></option>
		</select>
    <p class="help"><?php printf(I18n::__('Tags are necessary to create relations between %s automatically.'), Post_Type_Labels::getItemPluralName()) ?></p>
	</td>
</tr>

<tr>
  <th><?php I18n::_e('Post Categories') ?></th>
  <td>
    <select <?php disabled(True) ?> >
			<option <?php disabled(True) ?> ><?php I18n::_e('On') ?></option>
			<option <?php selected(True) ?> ><?php I18n::_e('Off') ?></option>
		</select><?php Mocking_Bird::printProNotice('unlock') ?>
    <p class="help"><?php printf(I18n::__('Enable post categories for %s.'), Post_Type_Labels::getItemPluralName()) ?></p>
	</td>
</tr>

<tr>
  <th><?php I18n::_e('Post Tags') ?></th>
  <td>
    <select <?php disabled(True) ?> >
			<option <?php disabled(True) ?> ><?php I18n::_e('On') ?></option>
			<option <?php selected(True) ?> ><?php I18n::_e('Off') ?></option>
		</select><?php Mocking_Bird::printProNotice('unlock') ?>
    <p class="help"><?php printf(I18n::__('Enable post tags for %s.'), Post_Type_Labels::getItemPluralName()) ?></p>
	</td>
</tr>
</table>
