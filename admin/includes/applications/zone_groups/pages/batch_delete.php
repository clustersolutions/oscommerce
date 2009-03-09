<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_zone_groups'); ?></div>
<div class="infoBoxContent">
  <form name="zDeleteBatch" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=batch_delete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_delete_zone_groups'); ?></p>

<?php
  $check_tax_zones_flag = array();

  $Qzones = $osC_Database->query('select geo_zone_id, geo_zone_name from :table_geo_zones where geo_zone_id in (":geo_zone_id") order by geo_zone_name');
  $Qzones->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
  $Qzones->bindRaw(':geo_zone_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qzones->execute();

  $names_string = '';

  while ( $Qzones->next() ) {
    if ( osC_ZoneGroups_Admin::hasTaxRate($Qzones->valueInt('geo_zone_id')) ) {
      $check_tax_zones_flag[] = $Qzones->value('geo_zone_name');
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qzones->valueInt('geo_zone_id')) . '<b>' . $Qzones->valueProtected('geo_zone_name') . ' (' . sprintf($osC_Language->get('total_entries'), osC_ZoneGroups_Admin::numberOfEntries($Qzones->valueInt('geo_zone_id'))) . ')</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  if ( empty($check_tax_zones_flag) ) {
    echo '<p align="center"><input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" /></p>';
  } else {
    echo '<p><b>' . $osC_Language->get('batch_delete_warning_group_in_use_tax_rate') . '</b></p>' .
         '<p>' . implode(', ', $check_tax_zones_flag) . '</p>';

    echo '<p align="center"><input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" /></p>';
  }
?>

  </form>
</div>
