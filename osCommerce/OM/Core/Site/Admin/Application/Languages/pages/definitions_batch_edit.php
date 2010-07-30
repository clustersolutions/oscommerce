<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('edit.png') . ' ' . osc_output_string_protected($_GET['group']); ?></h3>

  <form name="lDefineBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'BatchSaveDefinitions&Process&id=' . $_GET['id'] . '&group=' . $_GET['group']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_language_definitions'); ?></p>

  <fieldset>

<?php
  foreach ( $_POST['batch'] as $id ) {
    $OSCOM_ObjectInfo = new ObjectInfo(Languages::getDefinition($id));

    echo '<p><label for="def[' . $OSCOM_ObjectInfo->getProtected('definition_key') . ']">' . $OSCOM_ObjectInfo->getProtected('definition_key') . '</label>' . osc_draw_textarea_field('def[' . $OSCOM_ObjectInfo->get('definition_key') . ']', $OSCOM_ObjectInfo->get('definition_value')) . osc_draw_hidden_field('batch[]', $OSCOM_ObjectInfo->getInt('id')) . '</p>';
  }
?>

  </fieldset>

  <p><?php echo osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&group=' . $_GET['group']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
