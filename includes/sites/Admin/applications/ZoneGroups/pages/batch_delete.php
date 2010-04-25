<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('trash.png') . ' ' . OSCOM::getDef('action_heading_batch_delete_zone_groups'); ?></h3>

  <form name="zDeleteBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'action=BatchDelete'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_batch_delete_zone_groups'); ?></p>

<?php
  $check_tax_zones_flag = array();

  $Qzones = $osC_Database->query('select geo_zone_id, geo_zone_name from :table_geo_zones where geo_zone_id in (":geo_zone_id") order by geo_zone_name');
  $Qzones->bindRaw(':geo_zone_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qzones->execute();

  $names_string = '';

  while ( $Qzones->next() ) {
    if ( OSCOM_Site_Admin_Application_ZoneGroups_ZoneGroups::hasTaxRate($Qzones->valueInt('geo_zone_id')) ) {
      $check_tax_zones_flag[] = $Qzones->value('geo_zone_name');
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qzones->valueInt('geo_zone_id')) . '<b>' . $Qzones->valueProtected('geo_zone_name') . ' (' . sprintf(OSCOM::getDef('total_entries'), OSCOM_Site_Admin_Application_ZoneGroups_ZoneGroups::numberOfEntries($Qzones->valueInt('geo_zone_id'))) . ')</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  if ( empty($check_tax_zones_flag) ) {
    echo '<p>' . osc_draw_button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))) . '</p>';
  } else {
    echo '<p><b>' . OSCOM::getDef('batch_delete_warning_group_in_use_tax_rate') . '</b></p>' .
         '<p>' . implode(', ', $check_tax_zones_flag) . '</p>';

    echo '<p>' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'primary', 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))) . '</p>';
  }
?>

  </form>
</div>
