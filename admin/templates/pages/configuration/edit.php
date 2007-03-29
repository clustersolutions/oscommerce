<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Configuration_Admin::getData($_GET['cID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . $_GET['gID']), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }

  if ( !osc_empty($osC_ObjectInfo->get('set_function')) ) {
    $value_field = osc_call_user_func($osC_ObjectInfo->get('set_function'), $osC_ObjectInfo->get('configuration_value'));
  } else {
    $value_field = osc_draw_input_field('configuration_value', $osC_ObjectInfo->get('configuration_value'), 'style="width: 100%;"');
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->get('configuration_title'); ?></div>
<div class="infoBoxContent">
  <form name="cEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . $_GET['gID'] . '&cID=' . $osC_ObjectInfo->get('configuration_id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_parameter'); ?></p>

  <p><?php echo $osC_ObjectInfo->get('configuration_description'); ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->get('configuration_title') . ':</b><br />' . $value_field; ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . $_GET['gID']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
