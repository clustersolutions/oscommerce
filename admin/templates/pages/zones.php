<?php
/*
  $Id: zones.php,v 1.2 2004/07/22 22:46:19 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
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
      echo '      <tr onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);" onClick="document.location.href=\'' . tep_href_link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&zID=' . $Qzones->valueInt('zone_id')) . '\'">' . "\n";
    }
?>
        <td><?php echo $Qzones->value('countries_name'); ?></td>
        <td><?php echo $Qzones->value('zone_name'); ?></td>
        <td><?php echo $Qzones->value('zone_code'); ?></td>
        <td align="right">
<?php
    if (isset($zInfo) && ($Qzones->valueInt('zone_id') == $zInfo->zone_id)) {
      echo '<a href="#" onClick="toggleInfoBox(\'zEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="#" onClick="toggleInfoBox(\'zDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&zID=' . $Qzones->valueInt('zone_id') . '&action=zEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="' . tep_href_link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&zID=' . $Qzones->valueInt('zone_id') . '&action=zDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
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

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onClick="toggleInfoBox(\'zNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_zNew" <?php if ($action != 'zNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_INFO_HEADING_NEW_ZONE; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('zNew', FILENAME_ZONES, 'action=save'); ?>

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
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('zone_country_id', tep_get_countries()); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_INSERT . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'zDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($zInfo)) {
?>

<div id="infoBox_zEdit" <?php if ($action != 'zEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $zInfo->zone_name; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('zEdit', FILENAME_ZONES, 'page=' . $_GET['page'] . '&zID=' . $zInfo->zone_id . '&action=save'); ?>

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
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('zone_country_id', tep_get_countries(), $zInfo->countries_id); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_UPDATE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'zDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_zDelete" <?php if ($action != 'zDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $zInfo->zone_name; ?></div>
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

      echo '    <p align="center"><input type="button" value="' . IMAGE_DELETE . '" class="operationButton" onClick="document.location.href=\'' . tep_href_link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&zID=' . $zInfo->zone_id . '&action=deleteconfirm') . '\';"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'zDefault\');" class="operationButton"></p>' . "\n";
    } else {
      echo '    <p align="center"><input type="button" value="' . IMAGE_BACK . '" onClick="toggleInfoBox(\'zDefault\');" class="operationButton"></p>' . "\n";
    }
?>
  </div>
</div>

<?php
  }
?>
