<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_language_definition'); ?></div>
<div class="infoBoxContent">
  <form name="lNew" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . (isset($_GET['group']) ? '&group=' . $_GET['group'] : '') . '&action=insert_definition'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_new_language_definition'); ?></p>

  <fieldset>
    <div><label for="key"><?php echo $osC_Language->get('field_definition_key'); ?></label><?php echo osc_draw_input_field('key'); ?></div>
    <div><label><?php echo $osC_Language->get('field_definition_value'); ?></label>

<?php
  foreach ( $osC_Language->getAll() as $l ) {
    echo $osC_Language->showImage($l['code']) . '<br />' . osc_draw_textarea_field('value[' . $l['id'] . ']') . '<br />';
  }
?>

    </div>
    <div>
      <div style="width: 35%; float: left;"><label for="group"><?php echo $osC_Language->get('field_definition_group'); ?></label>

<?php
  $groups_array = array();

  foreach ( osc_toObjectInfo(osC_Languages_Admin::getDefinitionGroups($_GET[$osC_Template->getModule()]))->get('entries') as $value ) {
    $groups_array[] = array('id' => $value['content_group'],
                            'text' => $value['content_group']);
  }

  if ( !empty($groups_array) ) {
    echo osc_draw_pull_down_menu('group', $groups_array);
  }
?>

      </div>
      <div style="width: 35%; float: left;"><label for="group_new"><?php echo $osC_Language->get('field_definition_new_group'); ?></label><?php echo osc_draw_input_field('group_new'); ?></div>
    </div>
  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . (int)$_GET[$osC_Template->getModule()] . (isset($_GET['group']) ? '&group=' . $_GET['group'] : '')) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
