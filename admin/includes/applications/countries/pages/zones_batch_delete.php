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

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_zones'); ?></div>
<div class="infoBoxContent">
  <form name="cDeleteBatch" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . (int)$_GET[$osC_Template->getModule()] . '&action=batch_delete_zones'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_delete_zones'); ?></p>

<?php
  $check_address_book_flag = array();
  $check_tax_zones_flag = array();

  $Qzones = $osC_Database->query('select zone_id, zone_name, zone_code from :table_zones where zone_id in (":zone_id") order by zone_name');
  $Qzones->bindTable(':table_zones', TABLE_ZONES);
  $Qzones->bindRaw(':zone_id', implode('", "', array_unique(array_filter($_POST['batch'], 'is_numeric'))));
  $Qzones->execute();

  $names_string = '';

  while ( $Qzones->next() ) {
    $Qcheck = $osC_Database->query('select address_book_id from :table_address_book where entry_zone_id = :entry_zone_id limit 1');
    $Qcheck->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
    $Qcheck->bindInt(':entry_zone_id', $Qzones->valueInt('zone_id'));
    $Qcheck->execute();

    if ( $Qcheck->numberOfRows() === 1 ) {
      $check_address_book_flag[] = $Qzones->valueProtected('zone_name');
    }

    $Qcheck = $osC_Database->query('select association_id from :table_zones_to_geo_zones where zone_id = :zone_id limit 1');
    $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
    $Qcheck->bindInt(':zone_id', $Qzones->valueInt('zone_id'));
    $Qcheck->execute();

    if ( $Qcheck->numberOfRows() === 1 ) {
      $check_tax_zones_flag[] = $Qzones->valueProtected('zone_name');
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qzones->valueInt('zone_id')) . '<b>' . $Qzones->valueProtected('zone_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  if ( empty($check_address_book_flag) && empty($check_tax_zones_flag) ) {
    echo '<p align="center"><input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . (int)$_GET[$osC_Template->getModule()]) . '\';" class="operationButton" /></p>';
  } else {
    if ( !empty($check_address_book_flag) ) {
      echo '<p><b>' . $osC_Language->get('batch_delete_warning_zone_in_use_address_book') . '</b></p>' .
           '<p>' . implode(', ', $check_address_book_flag) . '</p>';
    }

    if ( !empty($check_tax_zones_flag) ) {
      echo '<p><b>' . $osC_Language->get('batch_delete_warning_zone_in_use_tax_zone') . '</b></p>' .
           '<p>' . implode(', ', $check_tax_zones_flag) . '</p>';
    }

    echo '<p align="center"><input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . (int)$_GET[$osC_Template->getModule()]) . '\';" class="operationButton" /></p>';
  }
?>

  </form>
</div>
