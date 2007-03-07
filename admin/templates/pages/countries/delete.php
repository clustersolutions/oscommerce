<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Countries_Admin::getData($_GET['cID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $osC_ObjectInfo->get('countries_name'); ?></div>
<div class="infoBoxContent">
  <form name="cDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cID=' . $osC_ObjectInfo->get('countries_id') . '&action=delete'); ?>" method="post">

<?php
  $can_be_deleted = true;

  $Qcheck = $osC_Database->query('select count(*) as total from :table_address_book where entry_country_id = :entry_country_id');
  $Qcheck->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
  $Qcheck->bindInt(':entry_country_id', $osC_ObjectInfo->get('countries_id'));
  $Qcheck->execute();

  if ( $Qcheck->valueInt('total') > 0 ) {
    $can_be_deleted = false;

    echo '    <p><b>' . sprintf(TEXT_INFO_DELETE_PROHIBITED_ADDRESS_BOOK, $Qcheck->valueInt('total')) . '</b></p>' . "\n";
  }

  $Qcheck = $osC_Database->query('select count(*) as total from :table_zones_to_geo_zones where zone_country_id = :zone_country_id');
  $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
  $Qcheck->bindInt(':zone_country_id', $osC_ObjectInfo->get('countries_id'));
  $Qcheck->execute();

  if ( $Qcheck->valueInt('total') > 0 ) {
    $can_be_deleted = false;

    echo '<p><b>' . sprintf(TEXT_INFO_DELETE_PROHIBITED_TAX_ZONES, $Qcheck->valueInt('total')) . '</b></p>' . "\n";
  }

  if ( $can_be_deleted === true ) {
    echo '    <p>' . TEXT_INFO_DELETE_INTRO . '</p>' . "\n" .
         '    <p><b>' . $osC_ObjectInfo->get('countries_name') . '</b></p>' . "\n";

    if ( $osC_ObjectInfo->get('total_zones') > 0 ) {
      echo '    <p><b>' . sprintf(TEXT_INFO_DELETE_COUNTRIES_WARNING, $osC_ObjectInfo->get('total_zones')) . '</b></p>' . "\n";
    }

    echo '  <p align="center">' . osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  } else {
    echo '  <p align="center"><input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  }
?>

  </form>
</div>
