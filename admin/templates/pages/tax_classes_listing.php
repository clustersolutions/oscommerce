<?php
/*
  $Id: tax_classes_listing.php,v 1.2 2004/07/22 22:45:58 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $Qclass = $osC_Database->query('select tax_class_id, tax_class_title from :table_tax_class where tax_class_id = :tax_class_id');
  $Qclass->bindTable(':table_tax_class', TABLE_TAX_CLASS);
  $Qclass->bindInt(':tax_class_id', $_GET['tcID']);
  $Qclass->execute();
?>

<h1><?php echo HEADING_TITLE . ': ' . $Qclass->value('tax_class_title'); ?></h1>

<div id="infoBox_trDefault" <?php if (!empty($entriesAction)) { echo 'style="display: none;"'; } ?>>
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
  $Qrates->bindInt(':tax_class_id', $_GET['tcID']);
  $Qrates->setBatchLimit($_GET['entriesPage'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qrates->execute();

  while ($Qrates->next()) {
    if (!isset($trInfo) && (!isset($_GET['trID']) || (isset($_GET['trID']) && ($_GET['trID'] == $Qrates->valueInt('tax_rates_id'))))) {
      $trInfo = new objectInfo(array_merge($Qclass->toArray(), $Qrates->toArray()));
    }

    if (isset($trInfo) && ($Qrates->valueInt('tax_rates_id') == $trInfo->tax_rates_id)) {
      echo '      <tr class="selected" title="' . $Qrates->valueProtected('tax_description') . '">' . "\n";
    } else {
      echo '      <tr onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);" onClick="document.location.href=\'' . tep_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $_GET['tcID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&trID=' . $Qrates->valueInt('tax_rates_id')) . '\';" title="' . $Qrates->valueProtected('tax_description') . '">' . "\n";
    }
?>
        <td><?php echo $Qrates->valueInt('tax_priority'); ?></td>
        <td><?php echo $Qrates->value('geo_zone_name'); ?></td>
        <td><?php echo tep_display_tax_value($Qrates->valueDecimal('tax_rate')); ?>%</td>
        <td align="right">
<?php
    if (isset($trInfo) && ($Qrates->valueInt('tax_rates_id') == $trInfo->tax_rates_id)) {
      echo '<a href="#" onClick="toggleInfoBox(\'trEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="#" onClick="toggleInfoBox(\'trDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $_GET['tcID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&trID=' . $Qrates->valueInt('tax_rates_id') . '&entriesAction=trEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="' . tep_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $_GET['tcID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&trID=' . $Qrates->valueInt('tax_rates_id') . '&entriesAction=trDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
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
      <td class="smallText" align="right"><?php echo $Qrates->displayBatchLinksPullDown('entriesPage'); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $_GET['tcID']) . '\';" class="infoBoxButton"> <input type="button" value="' . IMAGE_INSERT . '" onClick="toggleInfoBox(\'trNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_trNew" <?php if ($entriesAction != 'trNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_INFO_HEADING_NEW_TAX_RATE; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('trNew', FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $_GET['tcID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&entriesAction=save'); ?>

    <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ZONE_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo tep_geo_zones_pull_down('name="tax_zone_id"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_TAX_RATE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_rate', '', 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_TAX_RATE_DESCRIPTION . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_description', '', 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_TAX_RATE_PRIORITY . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_priority', '', 'style="width: 100%;"'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_INSERT . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'trDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($trInfo)) {
?>

<div id="infoBox_trEdit" <?php if ($entriesAction != 'trEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $trInfo->tax_class_title . ': ' . $trInfo->geo_zone_name; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('trEdit', FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $_GET['tcID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&trID=' . $trInfo->tax_rates_id  .'&entriesAction=save'); ?>

    <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ZONE_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo tep_geo_zones_pull_down('name="tax_zone_id"', $trInfo->geo_zone_id); ?></td>
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

    <p><?php echo TEXT_INFO_LAST_MODIFIED . ' ' . (($trInfo->last_modified > $trInfo->date_added) ? tep_date_short($trInfo->last_modified) : tep_date_short($trInfo->date_added)); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'trDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_trDelete" <?php if ($entriesAction != 'trDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $trInfo->tax_class_title . ': ' . $trInfo->geo_zone_name; ?></div>
  <div class="infoBoxContent">
    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $trInfo->tax_class_title . ': ' . $trInfo->geo_zone_name; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $_GET['tcID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&trID=' . $trInfo->tax_rates_id  .'&entriesAction=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'trDefault\');" class="operationButton">'; ?></p>
  </div>
</div>

<?php
  }
?>
