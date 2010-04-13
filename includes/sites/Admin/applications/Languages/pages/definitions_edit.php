<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(OSCOM_Site_Admin_Application_Languages_Languages::getDefinition($_GET['dID']));
?>

<h1><?php echo osc_link_object(OSCOM::getLink(), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('edit.png') . ' ' . osc_output_string_protected($_GET['group']); ?></h3>

  <form name="lDefine" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&group=' . $_GET['group'] . '&action=EditDefinition'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_language_definitions'); ?></p>

  <fieldset>
    <p><label for="def[<?php echo $osC_ObjectInfo->getProtected('definition_key'); ?>]"><?php echo $osC_ObjectInfo->getProtected('definition_key'); ?></label><?php echo osc_draw_textarea_field('def[' . $osC_ObjectInfo->get('definition_key') . ']', $osC_ObjectInfo->get('definition_value')); ?></p>
  </fieldset>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&group=' . $_GET['group']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
