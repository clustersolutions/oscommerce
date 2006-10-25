<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/tax.php');
  $osC_Tax = new osC_Tax_Admin();

  $Qclass = $osC_Database->query('select tax_class_id, tax_class_title from :table_tax_class where tax_class_id = :tax_class_id');
  $Qclass->bindTable(':table_tax_class', TABLE_TAX_CLASS);
  $Qclass->bindInt(':tax_class_id', $_GET[$osC_Template->getModule()]);
  $Qclass->execute();

  $zones_array = array();

  $Qzones = $osC_Database->query('select geo_zone_id, geo_zone_name from :table_geo_zones order by geo_zone_name');
  $Qzones->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
  $Qzones->execute();

  while ($Qzones->next()) {
    $zones_array[] = array('id' => $Qzones->valueInt('geo_zone_id'),
                           'text' => $Qzones->value('geo_zone_name'));
  }
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle() . ': ' . $Qclass->value('tax_class_title')); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_trDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_TAX_RATE_PRIORITY; ?></th>
        <th><?php echo TABLE_HEADING_ZONE; ?></th>
        <th><?php echo TABLE_HEADING_TAX_RATE; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>

<?php
  $Qrates = $osC_Database->query('select r.tax_rates_id, r.tax_priority, r.tax_rate, r.tax_description, r.date_added, r.last_modified, z.geo_zone_id, z.geo_zone_name from :table_tax_rates r, :table_geo_zones z where r.tax_class_id = :tax_class_id and r.tax_zone_id = z.geo_zone_id order by r.tax_priority, z.geo_zone_name');
  $Qrates->bindTable(':table_tax_rates', TABLE_TAX_RATES);
  $Qrates->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
  $Qrates->bindInt(':tax_class_id', $_GET[$osC_Template->getModule()]);
  $Qrates->setBatchLimit($_GET['entriesPage'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qrates->execute();

  while ($Qrates->next()) {
    if (!isset($trInfo) && (!isset($_GET['trID']) || (isset($_GET['trID']) && ($_GET['trID'] == $Qrates->valueInt('tax_rates_id'))))) {
      $trInfo = new objectInfo(array_merge($Qclass->toArray(), $Qrates->toArray()));
    }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" title="<?php echo $Qrates->valueProtected('tax_description'); ?>">
        <td><?php echo $Qrates->valueInt('tax_priority'); ?></td>
        <td><?php echo $Qrates->value('geo_zone_name'); ?></td>
        <td><?php echo $osC_Tax->displayTaxRateValue($Qrates->valueDecimal('tax_rate')); ?></td>
        <td align="right">

<?php
    if (isset($trInfo) && ($Qrates->valueInt('tax_rates_id') == $trInfo->tax_rates_id)) {
      echo osc_link_object('#', osc_icon('configure.png', IMAGE_EDIT), 'onclick="toggleInfoBox(\'trEdit\');"') . '&nbsp;' .
           osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'trDelete\');"');
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&entriesPage=' . $_GET['entriesPage'] . '&trID=' . $Qrates->valueInt('tax_rates_id') . '&action=trEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&entriesPage=' . $_GET['entriesPage'] . '&trID=' . $Qrates->valueInt('tax_rates_id') . '&action=trDelete'), osc_icon('trash.png', IMAGE_DELETE));
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
      <td class="smallText"><?php echo $Qrates->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_TAX_RATES); ?></td>
      <td class="smallText" align="right"><?php echo $Qrates->displayBatchLinksPullDown('entriesPage', $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&tcID=' . $_GET[$osC_Template->getModule()]) . '\';" class="infoBoxButton"> <input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'trNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_trNew" <?php if ($_GET['action'] != 'trNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_TAX_RATE; ?></div>
  <div class="infoBoxContent">
    <form name="trNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&entriesPage=' . $_GET['entriesPage'] . '&action=save_entry'); ?>" method="post">

    <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ZONE_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('tax_zone_id', $zones_array); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_TAX_RATE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_rate', null, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_TAX_RATE_DESCRIPTION . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_description', null, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_TAX_RATE_PRIORITY . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_priority', null, 'style="width: 100%;"'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_INSERT . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'trDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($trInfo)) {
?>

<div id="infoBox_trEdit" <?php if ($_GET['action'] != 'trEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $trInfo->tax_class_title . ': ' . $trInfo->geo_zone_name; ?></div>
  <div class="infoBoxContent">
    <form name="trEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&entriesPage=' . $_GET['entriesPage'] . '&trID=' . $trInfo->tax_rates_id  .'&action=save_entry'); ?>" method="post">

    <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ZONE_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('tax_zone_id', $zones_array, $trInfo->geo_zone_id); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_TAX_RATE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_rate', $trInfo->tax_rate, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_TAX_RATE_DESCRIPTION . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_description', $trInfo->tax_description, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_TAX_RATE_PRIORITY . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_priority', $trInfo->tax_priority, 'style="width: 100%;"'); ?></td>
      </tr>
    </table>

    <p><?php echo TEXT_INFO_LAST_MODIFIED . ' ' . (($trInfo->last_modified > $trInfo->date_added) ? osC_DateTime::getShort($trInfo->last_modified) : osC_DateTime::getShort($trInfo->date_added)); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'trDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_trDelete" <?php if ($_GET['action'] != 'trDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $trInfo->tax_class_title . ': ' . $trInfo->geo_zone_name; ?></div>
  <div class="infoBoxContent">
    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>

    <p><?php echo '<b>' . $trInfo->tax_class_title . ': ' . $trInfo->geo_zone_name . '</b>'; ?></p>

    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&entriesPage=' . $_GET['entriesPage'] . '&trID=' . $trInfo->tax_rates_id  .'&action=delete_entry_confirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'trDefault\');" class="operationButton">'; ?></p>
  </div>
</div>

<?php
  }
?>
