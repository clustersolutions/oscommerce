<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>

<script type="text/javascript"><!--
function update_zone(theForm) {
  var NumState = theForm.zone_id.options.length;
  var SelectedCountry = "";

  while(NumState > 0) {
    NumState--;
    theForm.zone_id.options[NumState] = null;
  }

  SelectedCountry = theForm.zone_country_id.options[theForm.zone_country_id.selectedIndex].value;

<?php echo tep_js_zone_list('SelectedCountry', 'theForm', 'zone_id'); ?>

}
//--></script>

<h1><?php echo HEADING_TITLE . ': ' . tep_get_geo_zone_name($_GET['zID']); ?></h1>

<div id="infoBox_zeDefault" <?php if (!empty($entriesAction)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_COUNTRY; ?></th>
        <th><?php echo TABLE_HEADING_COUNTRY_ZONE; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qentries = $osC_Database->query('select z2gz.association_id, z2gz.zone_country_id, c.countries_name, z2gz.zone_id, z2gz.geo_zone_id, z2gz.last_modified, z2gz.date_added, z.zone_name from :table_zones_to_geo_zones z2gz left join :table_countries c on (z2gz.zone_country_id = c.countries_id) left join :table_zones z on (z2gz.zone_id = z.zone_id) where z2gz.geo_zone_id = :geo_zone_id order by c.countries_name, z.zone_name');
  $Qentries->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
  $Qentries->bindTable(':table_zones', TABLE_ZONES);
  $Qentries->bindTable(':table_countries', TABLE_COUNTRIES);
  $Qentries->bindInt(':geo_zone_id', $_GET['zID']);
  $Qentries->setBatchLimit($_GET['entriesPage'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qentries->execute();

  while ($Qentries->next()) {
    if (!isset($zeInfo) && (!isset($_GET['zeID']) || (isset($_GET['zeID']) && ($_GET['zeID'] == $Qentries->valueInt('association_id'))))) {
      $zeInfo = new objectInfo($Qentries->toArray());

      if ($zeInfo->zone_country_id < 1) $zeInfo->countries_name = TEXT_ALL_COUNTRIES;
      if ($zeInfo->zone_id < 1) $zeInfo->zone_name = PLEASE_SELECT;
    }

    if (isset($zeInfo) && ($Qentries->valueInt('association_id') == $zeInfo->association_id)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . tep_href_link(FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&zeID=' . $Qentries->valueInt('association_id')) . '\';">' . "\n";
    }
?>
        <td><?php echo (($Qentries->valueInt('zone_country_id') > 0) ? $Qentries->value('countries_name') : TEXT_ALL_COUNTRIES); ?></td>
        <td><?php echo (($Qentries->valueInt('zone_id') > 0) ? $Qentries->value('zone_name') : PLEASE_SELECT); ?></td>
        <td align="right">
<?php
    if (isset($zeInfo) && ($Qentries->valueInt('association_id') == $zeInfo->association_id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'zeEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="#" onclick="toggleInfoBox(\'zeDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&zeID=' . $Qentries->valueInt('association_id') . '&entriesAction=zeEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="' . tep_href_link(FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&zeID=' . $Qentries->valueInt('association_id') . '&entriesAction=zeDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
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
      <td class="smallText"><?php echo $Qentries->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_COUNTRIES); ?></td>
      <td class="smallText" align="right"><?php echo $Qentries->displayBatchLinksPullDown('entriesPage', 'page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list'); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $_GET['zID']) . '\';" class="infoBoxButton"> <input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'zeNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_zeNew" <?php if ($entriesAction != 'zeNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_INFO_HEADING_NEW_SUB_ZONE; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('zeNew', FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list&entriesAction=zeSave'); ?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('zone_country_id', tep_get_countries(TEXT_ALL_COUNTRIES), '', 'onchange="update_zone(this.form);"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_ZONE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('zone_id', tep_prepare_country_zones_pull_down()); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'zeDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($zeInfo)) {
?>

<div id="infoBox_zeEdit" <?php if ($entriesAction != 'zeEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $zeInfo->countries_name . ': ' . $zeInfo->zone_name; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('zeEdit', FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&zeID=' . $zeInfo->association_id . '&entriesAction=zeSave'); ?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('zone_country_id', tep_get_countries(TEXT_ALL_COUNTRIES), $zeInfo->zone_country_id, 'onchange="update_zone(this.form);"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_ZONE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('zone_id', tep_prepare_country_zones_pull_down($zeInfo->zone_country_id), $zeInfo->zone_id); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'zeDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_zeDelete" <?php if ($entriesAction != 'zeDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $zeInfo->countries_name . ': ' . $zeInfo->zone_name; ?></div>
  <div class="infoBoxContent">
    <p><?php echo TEXT_INFO_DELETE_SUB_ZONE_INTRO; ?></p>
    <p><?php echo '<b>' . $zeInfo->countries_name . ': ' . $zeInfo->zone_name . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&zeID=' . $zeInfo->association_id . '&entriesAction=zeDeleteConfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'zeDefault\');" class="operationButton">'; ?></p>
  </div>
</div>

<?php
  }
?>
