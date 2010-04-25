<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(OSCOM_Site_Admin_Application_Configuration_Configuration::get($_GET['id']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }

  if ( !osc_empty($osC_ObjectInfo->get('set_function')) ) {
    $value_field = osc_call_user_func($osC_ObjectInfo->get('set_function'), $osC_ObjectInfo->get('configuration_value'), $osC_ObjectInfo->get('configuration_key'));
  } else {
    $value_field = osc_draw_input_field('configuration[' . $osC_ObjectInfo->get('configuration_key') . ']', $osC_ObjectInfo->get('configuration_value'));
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->getProtected('configuration_title'); ?></h3>

  <form name="cEdit" action="<?php echo OSCOM::getLink(null, null, 'gID=' . (int)$_GET['gID'] . '&action=Save'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_parameter'); ?></p>

  <fieldset>
    <p><label for="configuration[<?php echo $osC_ObjectInfo->get('configuration_key'); ?>]"><?php echo $osC_ObjectInfo->getProtected('configuration_title'); ?></label><?php echo $value_field; ?></p>
    <p><?php echo $osC_ObjectInfo->get('configuration_description'); ?></p>
  </fieldset>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'gID=' . (int)$_GET['gID']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
