<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $countries_array = array();

  foreach (osC_Address::getCountries() as $country) {
    $countries_array[] = array('id' => $country['id'],
                               'text' => $country['name']);
  }
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_zDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_COUNTRY_NAME; ?></th>
        <th><?php echo TABLE_HEADING_ZONE_NAME; ?></th>
        <th><?php echo TABLE_HEADING_ZONE_CODE; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qzones = $osC_Database->query('select z.zone_id, c.countries_id, c.countries_name, z.zone_name, z.zone_code, z.zone_country_id from :table_zones z, :table_countries c where z.zone_country_id = c.countries_id order by c.countries_name, z.zone_name');
  $Qzones->bindTable(':table_zones', TABLE_ZONES);
  $Qzones->bindTable(':table_countries', TABLE_COUNTRIES);
  $Qzones->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qzones->execute();

  while ($Qzones->next()) {
    if (!isset($zInfo) && (!isset($_GET['zID']) || (isset($_GET['zID']) && ($_GET['zID'] == $Qzones->valueInt('zone_id'))))) {
      $zInfo = new objectInfo($Qzones->toArray());
    }

    if (isset($zInfo) && ($Qzones->valueInt('zone_id') == $zInfo->zone_id)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_ZONES, 'page=' . $_GET['page'] . '&zID=' . $Qzones->valueInt('zone_id')) . '\'">' . "\n";
    }
?>
        <td><?php echo $Qzones->value('countries_name'); ?></td>
        <td><?php echo $Qzones->value('zone_name'); ?></td>
        <td><?php echo $Qzones->value('zone_code'); ?></td>
        <td align="right">
<?php
    if (isset($zInfo) && ($Qzones->valueInt('zone_id') == $zInfo->zone_id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'zEdit\');">' . osc_icon('configure.png', IMAGE_EDIT) . '</a>&nbsp;' .
           '<a href="#" onclick="toggleInfoBox(\'zDelete\');">' . osc_icon('trash.png', IMAGE_DELETE) . '</a>';
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_ZONES, 'page=' . $_GET['page'] . '&zID=' . $Qzones->valueInt('zone_id') . '&action=zEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_ZONES, 'page=' . $_GET['page'] . '&zID=' . $Qzones->valueInt('zone_id') . '&action=zDelete'), osc_icon('trash.png', IMAGE_DELETE));
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
      <td class="smallText"><?php echo $Qzones->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_ZONES); ?></td>
      <td class="smallText" align="right"><?php echo $Qzones->displayBatchLinksPullDown(); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'zNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_zNew" <?php if ($action != 'zNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_ZONE; ?></div>
  <div class="infoBoxContent">
    <form name="zNew" action="<?php echo osc_href_link_admin(FILENAME_ZONES, 'action=save'); ?>" method="post">

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ZONES_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('zone_name'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ZONES_CODE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('zone_code'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('zone_country_id', $countries_array); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_INSERT . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'zDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($zInfo)) {
?>

<div id="infoBox_zEdit" <?php if ($action != 'zEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $zInfo->zone_name; ?></div>
  <div class="infoBoxContent">
    <form name="zEdit" action="<?php echo osc_href_link_admin(FILENAME_ZONES, 'page=' . $_GET['page'] . '&zID=' . $zInfo->zone_id . '&action=save'); ?>" method="post">

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ZONES_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('zone_name', $zInfo->zone_name); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ZONES_CODE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('zone_code', $zInfo->zone_code); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('zone_country_id', $countries_array, $zInfo->countries_id); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_UPDATE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'zDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_zDelete" <?php if ($action != 'zDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $zInfo->zone_name; ?></div>
  <div class="infoBoxContent">
<?php
    $can_be_deleted = true;

    $Qcheck = $osC_Database->query('select count(*) as total from :table_address_book where entry_zone_id = :entry_zone_id');
    $Qcheck->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
    $Qcheck->bindInt(':entry_zone_id', $zInfo->zone_id);
    $Qcheck->execute();

    if ($Qcheck->valueInt('total') > 0) {
      $can_be_deleted = false;

      echo '    <p><b>' . sprintf(TEXT_INFO_DELETE_PROHIBITED_ADDRESS_BOOK, $Qcheck->valueInt('total')) . '</b></p>' . "\n";
    }

    $Qcheck = $osC_Database->query('select count(*) as total from :table_zones_to_geo_zones where zone_id = :zone_id');
    $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
    $Qcheck->bindInt(':zone_id', $zInfo->zone_id);
    $Qcheck->execute();

    if ($Qcheck->valueInt('total') > 0) {
      $can_be_deleted = false;

      echo '<p><b>' . sprintf(TEXT_INFO_DELETE_PROHIBITED_TAX_ZONES, $Qcheck->valueInt('total')) . '</b></p>' . "\n";
    }

    if ($can_be_deleted === true) {
      echo '    <p>' . TEXT_INFO_DELETE_INTRO . '</p>' . "\n" .
           '    <p><b>' . $zInfo->zone_name . '</b></p>' . "\n";

      echo '    <p align="center"><input type="button" value="' . IMAGE_DELETE . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_ZONES, 'page=' . $_GET['page'] . '&zID=' . $zInfo->zone_id . '&action=deleteconfirm') . '\';"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'zDefault\');" class="operationButton"></p>' . "\n";
    } else {
      echo '    <p align="center"><input type="button" value="' . IMAGE_BACK . '" onclick="toggleInfoBox(\'zDefault\');" class="operationButton"></p>' . "\n";
    }
?>
  </div>
</div>

<?php
  }
?>
