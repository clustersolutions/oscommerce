<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $zones_array = array();

  foreach ( osc_toObjectInfo(OSCOM_Site_Admin_Application_ZoneGroups_ZoneGroups::getAll(-1))->get('entries') as $group ) {
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
  <h3><?php echo osc_icon('new.png') . ' ' . OSCOM::getDef('action_heading_new_tax_rate'); ?></h3>

  <form name="rNew" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&action=EntrySave'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_new_tax_rate'); ?></p>

  <fieldset>
    <p><label for="tax_zone_id"><?php echo OSCOM::getDef('field_tax_rate_zone_group'); ?></label><?php echo osc_draw_pull_down_menu('tax_zone_id', $zones_array); ?></p>
    <p><label for="tax_rate"><?php echo OSCOM::getDef('field_tax_rate'); ?></label><?php echo osc_draw_input_field('tax_rate'); ?></p>
    <p><label for="tax_description"><?php echo OSCOM::getDef('field_tax_rate_description'); ?></label><?php echo osc_draw_input_field('tax_description'); ?></p>
    <p><label for="tax_priority"><?php echo OSCOM::getDef('field_tax_rate_priority'); ?></label><?php echo osc_draw_input_field('tax_priority'); ?></p>
  </fieldset>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
