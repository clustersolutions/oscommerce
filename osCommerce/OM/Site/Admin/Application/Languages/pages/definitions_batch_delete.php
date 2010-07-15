<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\OSCOM;
  use osCommerce\OM\ObjectInfo;
  use osCommerce\OM\Site\Admin\Application\Languages\Languages;
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('trash.png') . ' ' . osc_output_string_protected($_GET['group']); ?></h3>

  <form name="lDeleteBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&group=' . $_GET['group'] . '&action=BatchDeleteDefinitions'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_batch_delete_language_definitions'); ?></p>

  <fieldset>

<?php
  $names_string = '';

  foreach ( $_POST['batch'] as $id ) {
    $OSCOM_ObjectInfo = new ObjectInfo(Languages::getDefinition($id));

    $names_string .= osc_draw_hidden_field('batch[]', $OSCOM_ObjectInfo->getInt('id')) . '<b>' . $OSCOM_ObjectInfo->getProtected('definition_key') . '</b><br />';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -6);
  }

  echo '<p>' . $names_string . '</p>';
?>

  </fieldset>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&group=' . $_GET['group']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
