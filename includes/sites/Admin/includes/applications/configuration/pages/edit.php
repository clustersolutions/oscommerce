<?php
/*
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  $osCommerce-SIG$

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Configuration_Admin::get($_GET['cID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . (int)$_GET['gID']), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }

  if ( !osc_empty($osC_ObjectInfo->get('set_function')) ) {
    $value_field = osc_call_user_func($osC_ObjectInfo->get('set_function'), $osC_ObjectInfo->get('configuration_value'), $osC_ObjectInfo->get('configuration_key'));
  } else {
    $value_field = osc_draw_input_field('configuration[' . $osC_ObjectInfo->get('configuration_key') . ']', $osC_ObjectInfo->get('configuration_value'));
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->getProtected('configuration_title'); ?></h3>

  <form name="cEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . (int)$_GET['gID'] . '&action=save'); ?>" method="post">

  <p><?php echo __('introduction_edit_parameter'); ?></p>

  <fieldset>
    <p><label for="configuration[<?php echo $osC_ObjectInfo->get('configuration_key'); ?>]"><?php echo $osC_ObjectInfo->getProtected('configuration_title'); ?></label><?php echo $value_field; ?></p>
    <p><?php echo $osC_ObjectInfo->get('configuration_description'); ?></p>
  </fieldset>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => $osC_Language->get('button_save'))) . ' ' . osc_draw_button(array('href' => osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . (int)$_GET['gID']), 'priority' => 'secondary', 'icon' => 'close', 'title' => $osC_Language->get('button_cancel'))); ?></p>

  </form>
</div>
