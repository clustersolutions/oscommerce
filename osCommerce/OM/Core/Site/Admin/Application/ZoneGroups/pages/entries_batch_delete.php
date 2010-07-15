<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('trash.png') . ' ' . OSCOM::getDef('action_heading_batch_delete_zone_entries'); ?></h3>

  <form name="zDeleteBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&action=BatchDeleteEntries'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_batch_delete_zone_entries'); ?></p>

<?php
  $Qentries = $OSCOM_Database->query('select z2gz.association_id, z2gz.zone_country_id, c.countries_name, z2gz.zone_id, z.zone_name from :table_zones_to_geo_zones z2gz left join :table_countries c on (z2gz.zone_country_id = c.countries_id) left join :table_zones z on (z2gz.zone_id = z.zone_id) where z2gz.association_id in (":association_id") order by c.countries_name, z.zone_name');
  $Qentries->bindRaw(':association_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qentries->execute();

  $names_string = '';

  while ( $Qentries->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qentries->valueInt('association_id')) . '<b>' . (($Qentries->valueInt('zone_country_id') > 0) ? $Qentries->value('countries_name') : OSCOM::getDef('all_countries')) . ': ' . (($Qentries->valueInt('zone_id') > 0) ? $Qentries->value('zone_name') : OSCOM::getDef('all_zones')) . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  echo '<p>' . osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))) . '</p>';
?>

  </form>
</div>
