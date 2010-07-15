<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\ObjectInfo;
  use osCommerce\OM\Site\Admin\Application\ZoneGroups\ZoneGroups;
  use osCommerce\OM\OSCOM;

  $OSCOM_ObjectInfo = new ObjectInfo(ZoneGroups::get($_GET['id']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('trash.png') . ' ' . $OSCOM_ObjectInfo->getProtected('geo_zone_name'); ?></h3>

  <form name="zDelete" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'id=' . $OSCOM_ObjectInfo->getInt('geo_zone_id') . '&action=Delete'); ?>" method="post">

<?php
  if ( ZoneGroups::hasTaxRate($OSCOM_ObjectInfo->getInt('geo_zone_id')) ) {
?>

  <p><?php echo '<b>' . sprintf(OSCOM::getDef('delete_warning_group_in_use_tax_rate'), ZoneGroups::numberOfTaxRates($OSCOM_ObjectInfo->getInt('geo_zone_id'))) . '</b>'; ?></p>

  <p><?php echo osc_draw_button(array('href' => OSCOM::getLink(), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?></p>

<?php
  } else {
?>

  <p><?php echo OSCOM::getDef('introduction_delete_zone_group'); ?></p>

  <p><?php echo '<b>' . $OSCOM_ObjectInfo->getProtected('geo_zone_name') . ' (' . sprintf(OSCOM::getDef('total_entries'), $OSCOM_ObjectInfo->getInt('total_entries')) . ')</b>'; ?></p>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

<?php
  }
?>

  </form>
</div>
