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

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_zone_entries'); ?></div>
<div class="infoBoxContent">
  <form name="zeDeleteBatch" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&action=batch_delete_entries'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_delete_zone_entries'); ?></p>

<?php
  $Qentries = $osC_Database->query('select z2gz.association_id, z2gz.zone_country_id, c.countries_name, z2gz.zone_id, z.zone_name from :table_zones_to_geo_zones z2gz left join :table_countries c on (z2gz.zone_country_id = c.countries_id) left join :table_zones z on (z2gz.zone_id = z.zone_id) where z2gz.association_id in (":association_id") order by c.countries_name, z.zone_name');
  $Qentries->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
  $Qentries->bindTable(':table_zones', TABLE_ZONES);
  $Qentries->bindTable(':table_countries', TABLE_COUNTRIES);
  $Qentries->bindRaw(':association_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qentries->execute();

  $names_string = '';

  while ( $Qentries->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qentries->valueInt('association_id')) . '<b>' . (($Qentries->valueInt('zone_country_id') > 0) ? $Qentries->value('countries_name') : $osC_Language->get('all_countries')) . ': ' . (($Qentries->valueInt('zone_id') > 0) ? $Qentries->value('zone_name') : $osC_Language->get('all_zones')) . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  echo '<p align="center"><input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()]) . '\';" class="operationButton" /></p>';
?>

  </form>
</div>
