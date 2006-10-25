<?php
/*
  $Id$

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

<div id="infoBox_cDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_COUNTRY_NAME; ?></th>
        <th><?php echo TABLE_HEADING_COUNTRY_CODES; ?></th>
        <th><?php echo TABLE_HEADING_ZONES_TOTAL; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>

<?php
  $Qcountries = $osC_Database->query('select SQL_CALC_FOUND_ROWS countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, address_format from :table_countries order by countries_name');
  $Qcountries->bindTable(':table_countries', TABLE_COUNTRIES);
  $Qcountries->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qcountries->execute();

  while ($Qcountries->next()) {
    $Qzones = $osC_Database->query('select count(*) as total_zones from :table_zones where zone_country_id = :zone_country_id');
    $Qzones->bindTable(':table_zones', TABLE_ZONES);
    $Qzones->bindInt(':zone_country_id', $Qcountries->valueInt('countries_id'));
    $Qzones->execute();

    if (!isset($cInfo) && (!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $Qcountries->valueInt('countries_id'))))) {
      $cInfo = new objectInfo(array_merge($Qcountries->toArray(), $Qzones->toArray()));
    }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td><?php echo $Qcountries->value('countries_name'); ?></td>
        <td><?php echo $Qcountries->value('countries_iso_code_2') . '&nbsp;&nbsp;&nbsp;&nbsp;' . $Qcountries->value('countries_iso_code_3'); ?></td>
        <td><?php echo $Qzones->valueInt('total_zones'); ?></td>
        <td align="right">

<?php
    if (isset($cInfo) && ($Qcountries->valueInt('countries_id') == $cInfo->countries_id)) {
      echo osc_link_object('#', osc_icon('configure.png', IMAGE_EDIT), 'onclick="toggleInfoBox(\'cEdit\');"') . '&nbsp;' .
           osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'cDelete\');"');
    } else {
      echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cID=' . $Qcountries->valueInt('countries_id') . '&action=cEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cID=' . $Qcountries->valueInt('countries_id') . '&action=cDelete'), osc_icon('trash.png', IMAGE_DELETE));
    }
?>

        </td>
      </tr>

<?php
  }
?>

    </tbody>
  </table>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td><?php echo $Qcountries->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_COUNTRIES); ?></td>
      <td align="right"><?php echo $Qcountries->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'cNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_cNew" <?php if ($_GET['action'] != 'cNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_COUNTRY; ?></div>
  <div class="infoBoxContent">
    <form name="cNew" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=save'); ?>" method="post">

    <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_NAME . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('countries_name', null, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_CODE_2 . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('countries_iso_code_2', null, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_CODE_3 . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('countries_iso_code_3', null, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_INFO_ADDRESS_FORMAT . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_textarea_field('address_format'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($cInfo)) {
?>

<div id="infoBox_cEdit" <?php if ($_GET['action'] != 'cEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $cInfo->countries_name; ?></div>
  <div class="infoBoxContent">
    <form name="cEdit" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=save'); ?>" method="post">

    <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_NAME . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('countries_name', $cInfo->countries_name, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_CODE_2 . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('countries_iso_code_2', $cInfo->countries_iso_code_2, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_CODE_3 . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('countries_iso_code_3', $cInfo->countries_iso_code_3, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_INFO_ADDRESS_FORMAT . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_textarea_field('address_format', $cInfo->address_format); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_cDelete" <?php if ($_GET['action'] != 'cDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $cInfo->countries_name; ?></div>
  <div class="infoBoxContent">

<?php
    $can_be_deleted = true;

    $Qcheck = $osC_Database->query('select count(*) as total from :table_address_book where entry_country_id = :entry_country_id');
    $Qcheck->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
    $Qcheck->bindInt(':entry_country_id', $cInfo->countries_id);
    $Qcheck->execute();

    if ($Qcheck->valueInt('total') > 0) {
      $can_be_deleted = false;

      echo '    <p><b>' . sprintf(TEXT_INFO_DELETE_PROHIBITED_ADDRESS_BOOK, $Qcheck->valueInt('total')) . '</b></p>' . "\n";
    }

    $Qcheck = $osC_Database->query('select count(*) as total from :table_zones_to_geo_zones where zone_country_id = :zone_country_id');
    $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
    $Qcheck->bindInt(':zone_country_id', $cInfo->countries_id);
    $Qcheck->execute();

    if ($Qcheck->valueInt('total') > 0) {
      $can_be_deleted = false;

      echo '<p><b>' . sprintf(TEXT_INFO_DELETE_PROHIBITED_TAX_ZONES, $Qcheck->valueInt('total')) . '</b></p>' . "\n";
    }

    if ($can_be_deleted === true) {
      echo '    <p>' . TEXT_INFO_DELETE_INTRO . '</p>' . "\n" .
           '    <p><b>' . $cInfo->countries_name . '</b></p>' . "\n";

      if ($cInfo->total_zones > 0) {
        echo '    <p><b>' . sprintf(TEXT_INFO_DELETE_COUNTRIES_WARNING, $cInfo->total_zones) . '</b></p>' . "\n";
      }

      echo '    <p align="center"><input type="button" value="' . IMAGE_DELETE . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=deleteconfirm') . '\';"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton"></p>' . "\n";
    } else {
      echo '    <p align="center"><input type="button" value="' . IMAGE_BACK . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton"></p>' . "\n";
    }
?>

  </div>
</div>

<?php
  }
?>
