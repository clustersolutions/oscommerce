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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Countries_Admin::get($_GET['cID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_ObjectInfo->getProtected('countries_name'); ?></div>
<div class="infoBoxContent">
  <form name="cDelete" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $osC_ObjectInfo->getInt('countries_id') . '&action=delete'); ?>" method="post">

<?php
  $can_be_deleted = true;

  $Qcheck = $osC_Database->query('select count(*) as total from :table_address_book where entry_country_id = :entry_country_id');
  $Qcheck->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
  $Qcheck->bindInt(':entry_country_id', $osC_ObjectInfo->getInt('countries_id'));
  $Qcheck->execute();

  if ( $Qcheck->valueInt('total') > 0 ) {
    $can_be_deleted = false;

    echo '<p><b>' . sprintf($osC_Language->get('delete_warning_country_in_use_address_book'), $Qcheck->valueInt('total')) . '</b></p>' . "\n";
  }

  $Qcheck = $osC_Database->query('select count(*) as total from :table_zones_to_geo_zones where zone_country_id = :zone_country_id');
  $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
  $Qcheck->bindInt(':zone_country_id', $osC_ObjectInfo->getInt('countries_id'));
  $Qcheck->execute();

  if ( $Qcheck->valueInt('total') > 0 ) {
    $can_be_deleted = false;

    echo '<p><b>' . sprintf($osC_Language->get('delete_warning_country_in_use_tax_zone'), $Qcheck->valueInt('total')) . '</b></p>' . "\n";
  }

  if ( $can_be_deleted === true ) {
    $country_name = $osC_ObjectInfo->getProtected('countries_name');

    if ( $osC_ObjectInfo->getInt('total_zones') > 0 ) {
      $country_name .= ' (' . sprintf($osC_Language->get('total_zones'), $osC_ObjectInfo->getInt('total_zones')) . ')';
    }

    echo '<p>' . $osC_Language->get('introduction_delete_country') . '</p>' .
         '<p><b>' . $country_name . '</b></p>' .
         '<p align="center">' . osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" /></p>';
  } else {
    echo '<p align="center"><input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" /></p>';
  }
?>

  </form>
</div>
