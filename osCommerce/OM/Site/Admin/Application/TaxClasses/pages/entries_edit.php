<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\ObjectInfo;
  use osCommerce\OM\Site\Admin\Application\TaxClasses\TaxClasses;
  use osCommerce\OM\Site\Admin\Application\ZoneGroups\ZoneGroups;
  use osCommerce\OM\OSCOM;

  $OSCOM_ObjectInfo = new ObjectInfo(TaxClasses::getEntry($_GET['rID']));

  $zones_array = array();

  foreach ( osc_toObjectInfo(ZoneGroups::getAll(-1))->get('entries') as $group ) {
    $zones_array[] = array('id' => $group['geo_zone_id'],
                           'text' => $group['geo_zone_name']);
  }
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('tax_class_title') . ': ' . $OSCOM_ObjectInfo->getProtected('geo_zone_name'); ?></h3>

  <form name="rEdit" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&rID=' . $OSCOM_ObjectInfo->getInt('tax_rates_id') . '&action=EntrySave'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_tax_rate'); ?></p>

  <fieldset>
    <p><label for="tax_zone_id"><?php echo OSCOM::getDef('field_tax_rate_zone_group'); ?></label><?php echo osc_draw_pull_down_menu('tax_zone_id', $zones_array, $OSCOM_ObjectInfo->getInt('geo_zone_id')); ?></p>
    <p><label for="tax_rate"><?php echo OSCOM::getDef('field_tax_rate'); ?></label><?php echo osc_draw_input_field('tax_rate', $OSCOM_ObjectInfo->get('tax_rate')); ?></p>
    <p><label for="tax_description"><?php echo OSCOM::getDef('field_tax_rate_description'); ?></label><?php echo osc_draw_input_field('tax_description', $OSCOM_ObjectInfo->get('tax_description')); ?></p>
    <p><label for="tax_priority"><?php echo OSCOM::getDef('field_tax_rate_priority'); ?></label><?php echo osc_draw_input_field('tax_priority', $OSCOM_ObjectInfo->getInt('tax_priority')); ?></p>
  </fieldset>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
