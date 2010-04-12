<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo osc_link_object(OSCOM::getLink(), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('edit.png') . ' ' . osc_output_string_protected($_GET['group']); ?></h3>

  <form name="lDefineBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&group=' . $_GET['group'] . '&action=BatchSaveDefinitions'); ?>" method="post">

  <p><?php echo __('introduction_edit_language_definitions'); ?></p>

  <fieldset>

<?php
  foreach ( $_POST['batch'] as $id ) {
    $osC_ObjectInfo = new osC_ObjectInfo(OSCOM_Site_Admin_Application_Languages_Languages::getDefinition($id));

    echo '<p><label for="def[' . $osC_ObjectInfo->getProtected('definition_key') . ']">' . $osC_ObjectInfo->getProtected('definition_key') . '</label>' . osc_draw_textarea_field('def[' . $osC_ObjectInfo->get('definition_key') . ']', $osC_ObjectInfo->get('definition_value')) . osc_draw_hidden_field('batch[]', $osC_ObjectInfo->getInt('id')) . '</p>';
  }
?>

  </fieldset>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => __('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&group=' . $_GET['group']), 'priority' => 'secondary', 'icon' => 'close', 'title' => __('button_cancel'))); ?></p>

  </form>
</div>
