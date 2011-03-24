<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\Site\Admin\Application\Configuration\Configuration;
  use osCommerce\OM\Core\OSCOM;

  $OSCOM_ObjectInfo = new ObjectInfo(Configuration::getEntry($_GET['pID']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }

  if ( !osc_empty($OSCOM_ObjectInfo->get('set_function')) ) {
    $value_field = osc_call_user_func($OSCOM_ObjectInfo->get('set_function'), $OSCOM_ObjectInfo->get('configuration_value'), $OSCOM_ObjectInfo->get('configuration_key'));
  } else {
    $value_field = osc_draw_input_field('configuration[' . $OSCOM_ObjectInfo->get('configuration_key') . ']', $OSCOM_ObjectInfo->get('configuration_value'));
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('configuration_title'); ?></h3>

  <form name="cEdit" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'EntrySave&Process&id=' . $_GET['id']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_parameter'); ?></p>

  <fieldset>
    <p><label for="configuration[<?php echo $OSCOM_ObjectInfo->get('configuration_key'); ?>]"><?php echo $OSCOM_ObjectInfo->getProtected('configuration_title'); ?></label><?php echo $value_field; ?></p>
    <p><?php echo $OSCOM_ObjectInfo->get('configuration_description'); ?></p>
  </fieldset>

  <p><?php echo osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
