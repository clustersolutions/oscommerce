<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_tcDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_TAX_CLASSES; ?></th>
        <th><?php echo TABLE_HEADING_TAX_RATES_TOTAL; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qclasses = $osC_Database->query('select tax_class_id, tax_class_title, tax_class_description, last_modified, date_added from :table_tax_class order by tax_class_title');
  $Qclasses->bindTable(':table_tax_class', TABLE_TAX_CLASS);
  $Qclasses->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qclasses->execute();

  while ($Qclasses->next()) {
    $Qrates = $osC_Database->query('select count(*) as total_tax_rates from :table_tax_rates where tax_class_id = :tax_class_id');
    $Qrates->bindTable(':table_tax_rates', TABLE_TAX_RATES);
    $Qrates->bindInt(':tax_class_id', $Qclasses->valueInt('tax_class_id'));
    $Qrates->execute();

    if (!isset($tcInfo) && (!isset($_GET['tcID']) || (isset($_GET['tcID']) && ($_GET['tcID'] == $Qclasses->valueInt('tax_class_id'))))) {
      $tcInfo = new objectInfo(array_merge($Qclasses->toArray(), $Qrates->toArray()));
    }

    if (isset($tcInfo) && ($Qclasses->valueInt('tax_class_id') == $tcInfo->tax_class_id)) {
      echo '      <tr class="selected" title="' . $Qclasses->valueProtected('tax_class_description') . '">' . "\n";
    } else {
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . tep_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $Qclasses->valueInt('tax_class_id')) . '\'" title="' . $Qclasses->valueProtected('tax_class_description') . '">' . "\n";
    }
?>
        <td><?php echo '<a href="' . tep_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $Qclasses->valueInt('tax_class_id') . '&action=list') . '">' . tep_image('images/icons/folder.gif', ICON_FOLDER) . '&nbsp;' . $Qclasses->value('tax_class_title') . '</a>'; ?></td>
        <td><?php echo $Qrates->valueInt('total_tax_rates'); ?></td>
        <td align="right">
<?php
    if (isset($tcInfo) && ($Qclasses->valueInt('tax_class_id') == $tcInfo->tax_class_id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'tcEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="#" onclick="toggleInfoBox(\'tcDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $Qclasses->valueInt('tax_class_id') . '&action=tcEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="' . tep_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $Qclasses->valueInt('tax_class_id') . '&action=tcDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
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
      <td class="smallText"><?php echo $Qclasses->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES); ?></td>
      <td class="smallText" align="right"><?php echo $Qclasses->displayBatchLinksPullDown(); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'tcNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_tcNew" <?php if ($action != 'tcNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_INFO_HEADING_NEW_TAX_CLASS; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('tcNew', FILENAME_TAX_CLASSES, 'action=save'); ?>

    <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CLASS_TITLE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_class_title', '', 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CLASS_DESCRIPTION . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_class_description', '', 'style="width: 100%;"'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_INSERT . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'tcDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($tcInfo)) {
?>

<div id="infoBox_tcEdit" <?php if ($action != 'tcEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $tcInfo->tax_class_title; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('tcEdit', FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $tcInfo->tax_class_id . '&action=save'); ?>

    <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CLASS_TITLE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_class_title', $tcInfo->tax_class_title, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CLASS_DESCRIPTION . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_class_description', $tcInfo->tax_class_description, 'style="width: 100%;"'); ?></td>
      </tr>
    </table>

    <p><?php echo TEXT_INFO_LAST_MODIFIED . ' ' . (($tcInfo->last_modified > $tcInfo->date_added) ? tep_date_short($tcInfo->last_modified) : tep_date_short($tcInfo->date_added)); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'tcDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_tcDelete" <?php if ($action != 'tcDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $tcInfo->tax_class_title; ?></div>
  <div class="infoBoxContent">
<?php
    $Qcheck = $osC_Database->query('select products_id from :table_products where products_tax_class_id = :products_tax_class_id limit 1');
    $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
    $Qcheck->bindInt(':products_tax_class_id', $tcInfo->tax_class_id);
    $Qcheck->execute();

    if ($Qcheck->numberOfRows() > 0) {
?>
    <p><?php echo '<b>' . TEXT_INFO_DELETE_PROHIBITED . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="toggleInfoBox(\'tcDefault\');" class="operationButton">'; ?></p>
<?php
    } else {
?>
    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $tcInfo->tax_class_title; ?></p>
<?php
      if ($tcInfo->total_tax_rates > 0) {
        echo '    <p><b>' . sprintf(TEXT_INFO_DELETE_TAX_RATES_WARNING, $tcInfo->total_tax_rates) . '</b></p>' . "\n";
      }
?>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $tcInfo->tax_class_id . '&action=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'tcDefault\');" class="operationButton">'; ?></p>
<?php
    }
?>
  </div>
</div>

<?php
  }
?>
