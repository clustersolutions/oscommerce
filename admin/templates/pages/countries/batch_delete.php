<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' Batch Delete'; ?></div>
<div class="infoBoxContent">
  <form name="cDeleteBatch" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo TEXT_DELETE_BATCH_INTRO; ?></p>

<?php
  $check_address_book_flag = array();
  $check_tax_zones_flag = array();

  $Qcountries = $osC_Database->query('select countries_id, countries_name from :table_countries where countries_id in (":countries_id") order by countries_name');
  $Qcountries->bindTable(':table_countries', TABLE_COUNTRIES);
  $Qcountries->bindRaw(':countries_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qcountries->execute();

  $names_string = '';

  while ($Qcountries->next()) {
    $Qcheck = $osC_Database->query('select address_book_id from :table_address_book where entry_country_id = :entry_country_id limit 1');
    $Qcheck->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
    $Qcheck->bindInt(':entry_country_id', $Qcountries->valueInt('countries_id'));
    $Qcheck->execute();

    if ( $Qcheck->numberOfRows() === 1 ) {
      $check_address_book_flag[] = $Qcountries->value('countries_name');
    }

    $Qcheck = $osC_Database->query('select association_id from :table_zones_to_geo_zones where zone_country_id = :zone_country_id limit 1');
    $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
    $Qcheck->bindInt(':zone_country_id', $Qcountries->valueInt('countries_id'));
    $Qcheck->execute();

    if ( $Qcheck->numberOfRows() === 1 ) {
      $check_tax_zones_flag[] = $Qcountries->value('countries_name');
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qcountries->valueInt('countries_id')) . '<b>' . $Qcountries->value('countries_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  if ( empty($check_address_book_flag) && empty($check_tax_zones_flag) ) {
    echo '<p align="center"><input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  } else {
    if ( !empty($check_address_book_flag) ) {
      echo '<p><b>' . TEXT_INFO_BATCH_DELETE_PROHIBITED_ADDRESS_BOOK . '</b></p>' .
           '<p>' . implode(', ', $check_address_book_flag) . '</p>';
    }

    if ( !empty($check_tax_zones_flag) ) {
      echo '<p><b>' . TEXT_INFO_BATCH_DELETE_PROHIBITED_TAX_ZONES . '</b></p>' .
           '<p>' . implode(', ', $check_tax_zones_flag) . '</p>';
    }

    echo '<p align="center"><input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  }
?>

  </form>
</div>
