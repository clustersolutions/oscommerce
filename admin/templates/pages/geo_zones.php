<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo HEADING_TITLE;?></h1>

<div id="infoBox_zDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_TAX_ZONES; ?></th>
        <th><?php echo TABLE_HEADING_TOTAL_ENTRIES; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qzones = $osC_Database->query('select geo_zone_id, geo_zone_name, geo_zone_description, last_modified, date_added from :table_geo_zones order by geo_zone_name');
  $Qzones->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
  $Qzones->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qzones->execute();

  while ($Qzones->next()) {
    $Qentries = $osC_Database->query('select count(*) as total_entries from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id');
    $Qentries->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
    $Qentries->bindInt(':geo_zone_id', $Qzones->valueInt('geo_zone_id'));
    $Qentries->execute();

    if (!isset($zInfo) && (!isset($_GET['zID']) || (isset($_GET['zID']) && ($_GET['zID'] == $Qzones->valueInt('geo_zone_id'))))) {
      $zInfo = new objectInfo(array_merge($Qzones->toArray(), $Qentries->toArray()));
    }

    if (isset($zInfo) && ($Qzones->valueInt('geo_zone_id') == $zInfo->geo_zone_id)) {
      echo '      <tr class="selected" title="' . $Qzones->valueProtected('geo_zone_description') . '">' . "\n";
    } else {
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $Qzones->valueInt('geo_zone_id')) . '\';"  title="' . $Qzones->valueProtected('geo_zone_description') . '">' . "\n";
    }
?>
        <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $Qzones->valueInt('geo_zone_id') . '&action=list'), osc_image('images/icons/folder.gif', ICON_FOLDER) . '&nbsp;' . $Qzones->value('geo_zone_name')); ?></td>
        <td><?php echo $Qentries->valueInt('total_entries'); ?></td>
        <td align="right">
<?php
    if (isset($zInfo) && ($Qzones->valueInt('geo_zone_id') == $zInfo->geo_zone_id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'zEdit\');">' . osc_icon('configure.png', IMAGE_EDIT) . '</a>&nbsp;' .
           '<a href="#" onclick="toggleInfoBox(\'zDelete\');">' . osc_icon('trash.png', IMAGE_DELETE) . '</a>';
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $Qzones->valueInt('geo_zone_id') . '&action=zEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $Qzones->valueInt('geo_zone_id') . '&action=zDelete'), osc_icon('trash.png', IMAGE_DELETE));
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
      <td class="smallText"><?php echo $Qzones->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_TAX_ZONES); ?></td>
      <td class="smallText" align="right"><?php echo $Qzones->displayBatchLinksPullDown(); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'zNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_zNew" <?php if ($action != 'zNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_ZONE; ?></div>
  <div class="infoBoxContent">
    <form name="zNew" action="<?php echo osc_href_link_admin(FILENAME_GEO_ZONES, 'action=save'); ?>" method="post">

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ZONE_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('geo_zone_name', null, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ZONE_DESCRIPTION . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('geo_zone_description', null, 'style="width: 100%"'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'zDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($zInfo)) {
?>

<div id="infoBox_zEdit" <?php if ($action != 'zEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_ZONE; ?></div>
  <div class="infoBoxContent">
    <form name="zEdit" action="<?php echo osc_href_link_admin(FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $zInfo->geo_zone_id . '&action=save'); ?>" method="post">

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ZONE_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('geo_zone_name', $zInfo->geo_zone_name, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ZONE_DESCRIPTION . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('geo_zone_description', $zInfo->geo_zone_description, 'style="width: 100%"'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'zDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_zDelete" <?php if ($action != 'zDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $zInfo->geo_zone_name; ?></div>
  <div class="infoBoxContent">
<?php
    $Qcheck = $osC_Database->query('select tax_zone_id from :table_tax_rates where tax_zone_id = :tax_zone_id limit 1');
    $Qcheck->bindTable(':table_tax_rates', TABLE_TAX_RATES);
    $Qcheck->bindInt(':tax_zone_id', $zInfo->geo_zone_id);
    $Qcheck->execute();

    if ($Qcheck->numberOfRows() > 0) {
?>
    <p><?php echo '<b>' . TEXT_INFO_DELETE_PROHIBITED . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="toggleInfoBox(\'zDefault\');" class="operationButton">'; ?></p>
<?php
    } else {
?>
    <p><?php echo TEXT_INFO_DELETE_ZONE_INTRO; ?></p>
    <p><?php echo '<b>' . $zInfo->geo_zone_name . '</b>'; ?></p>
<?php
      if ($zInfo->total_entries > 0) {
        echo '    <p><b>' . sprintf(TEXT_INFO_DELETE_ZONE_WARNING, $zInfo->total_entries) . '</b></p>' . "\n";
      }
?>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $zInfo->geo_zone_id . '&action=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'zDefault\');" class="operationButton">'; ?></p>
<?php
    }
?>
  </div>
</div>

<?php
  }
?>
