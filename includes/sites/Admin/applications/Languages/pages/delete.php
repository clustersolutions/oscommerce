<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(OSCOM_Site_Admin_Application_Languages_Languages::get($_GET['id']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('trash.png') . ' ' . $osC_ObjectInfo->getProtected('name'); ?></h3>

<?php
  if ( $osC_ObjectInfo->get('code') == DEFAULT_LANGUAGE ) {
?>

  <p><?php echo '<b>' . OSCOM::getDef('introduction_delete_language_invalid') . '</b>'; ?></p>

  <p><?php echo osc_draw_button(array('href' => OSCOM::getLink(), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?></p>

<?php
  } else {
?>

  <form name="lDelete" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&action=Delete'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_delete_language'); ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->getProtected('name') . '</b>'; ?></p>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>

<?php
  }
?>

</div>
