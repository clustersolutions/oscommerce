<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $Qzone = $osC_Database->query('select geo_zone_name from :table_geo_zones where geo_zone_id = :geo_zone_id');
  $Qzone->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
  $Qzone->bindInt(':geo_zone_id', $_GET['zID']);
  $Qzone->execute();

  $countries_array = array(array('id' => '', 'text' => TEXT_ALL_COUNTRIES));

  foreach (osC_Address::getCountries() as $country) {
    $countries_array[] = array('id' => $country['id'],
                               'text' => $country['name']);
  }
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

<?php echo osc_js_zone_list('SelectedCountry', 'theForm', 'zone_id'); ?>

}
//--></script>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list'), $osC_Template->getPageTitle() . ': ' . $Qzone->value('geo_zone_name')); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_zeDefault" <?php if (!empty($_GET['entriesAction'])) { echo 'style="display: none;"'; } ?>>
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
  $Qentries->execute();

  while ($Qentries->next()) {
    if (!isset($zeInfo) && (!isset($_GET['zeID']) || (isset($_GET['zeID']) && ($_GET['zeID'] == $Qentries->valueInt('association_id'))))) {
      $zeInfo = new objectInfo($Qentries->toArray());

      if ($zeInfo->zone_country_id < 1) {
        $zeInfo->countries_name = TEXT_ALL_COUNTRIES;
      }

      if ($zeInfo->zone_id < 1) {
        $zeInfo->zone_name = PLEASE_SELECT;
      }
    }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td><?php echo (($Qentries->valueInt('zone_country_id') > 0) ? $Qentries->value('countries_name') : TEXT_ALL_COUNTRIES); ?></td>
        <td><?php echo (($Qentries->valueInt('zone_id') > 0) ? $Qentries->value('zone_name') : PLEASE_SELECT); ?></td>
        <td align="right">

<?php
    if (isset($zeInfo) && ($Qentries->valueInt('association_id') == $zeInfo->association_id)) {
      echo osc_link_object('#', osc_icon('configure.png', IMAGE_EDIT), 'onclick="toggleInfoBox(\'zeEdit\');"') . '&nbsp;' .
           osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'zeDelete\');"');
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list&zeID=' . $Qentries->valueInt('association_id') . '&entriesAction=zeEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list&zeID=' . $Qentries->valueInt('association_id') . '&entriesAction=zeDelete'), osc_icon('trash.png', IMAGE_DELETE));
    }
?>

        </td>
      </tr>

<?php
    }
?>

    </tbody>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&zID=' . $_GET['zID']) . '\';" class="infoBoxButton"> <input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'zeNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_zeNew" <?php if ($_GET['entriesAction'] != 'zeNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_SUB_ZONE; ?></div>
  <div class="infoBoxContent">
    <form name="zeNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list&entriesAction=save'); ?>" method="post">

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('zone_country_id', $countries_array, null, 'onchange="update_zone(this.form);"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_ZONE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('zone_id', null); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'zeDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($zeInfo)) {
    $zones_array = array();

    foreach (osC_Address::getZones($zeInfo->zone_country_id) as $zone) {
      $zones_array[] = array('id' => $zone['id'],
                             'text' => $zone['name']);
    }
?>

<div id="infoBox_zeEdit" <?php if ($_GET['entriesAction'] != 'zeEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $zeInfo->countries_name . ': ' . $zeInfo->zone_name; ?></div>
  <div class="infoBoxContent">
    <form name="zeEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list&zeID=' . $zeInfo->association_id . '&entriesAction=save'); ?>" method="post">

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('zone_country_id', $countries_array, $zeInfo->zone_country_id, 'onchange="update_zone(this.form);"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_ZONE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('zone_id', $zones_array, $zeInfo->zone_id); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'zeDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_zeDelete" <?php if ($_GET['entriesAction'] != 'zeDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $zeInfo->countries_name . ': ' . $zeInfo->zone_name; ?></div>
  <div class="infoBoxContent">
    <p><?php echo TEXT_INFO_DELETE_SUB_ZONE_INTRO; ?></p>

    <p><?php echo '<b>' . $zeInfo->countries_name . ': ' . $zeInfo->zone_name . '</b>'; ?></p>

    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list&zeID=' . $zeInfo->association_id . '&entriesAction=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'zeDefault\');" class="operationButton">'; ?></p>
  </div>
</div>

<?php
  }
?>
