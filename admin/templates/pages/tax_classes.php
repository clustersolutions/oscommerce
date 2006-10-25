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

<div id="infoBox_tcDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
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
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" title="<?php echo $Qclasses->valueProtected('tax_class_description'); ?>">
        <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $Qclasses->valueInt('tax_class_id') . '&page=' . $_GET['page']), osc_image('images/icons/folder.gif', ICON_FOLDER) . '&nbsp;' . $Qclasses->value('tax_class_title')); ?></td>
        <td><?php echo $Qrates->valueInt('total_tax_rates'); ?></td>
        <td align="right">

<?php
    if (isset($tcInfo) && ($Qclasses->valueInt('tax_class_id') == $tcInfo->tax_class_id)) {
      echo osc_link_object('#', osc_icon('configure.png', IMAGE_EDIT), 'onclick="toggleInfoBox(\'tcEdit\');"') . '&nbsp;' .
           osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'tcDelete\');"');
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&tcID=' . $Qclasses->valueInt('tax_class_id') . '&action=tcEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&tcID=' . $Qclasses->valueInt('tax_class_id') . '&action=tcDelete'), osc_icon('trash.png', IMAGE_DELETE));
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
      <td class="smallText" align="right"><?php echo $Qclasses->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'tcNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_tcNew" <?php if ($_GET['action'] != 'tcNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_TAX_CLASS; ?></div>
  <div class="infoBoxContent">
    <form name="tcNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=save'); ?>" method="post">

    <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CLASS_TITLE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_class_title', null, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CLASS_DESCRIPTION . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('tax_class_description', null, 'style="width: 100%;"'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_INSERT . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'tcDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($tcInfo)) {
?>

<div id="infoBox_tcEdit" <?php if ($_GET['action'] != 'tcEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $tcInfo->tax_class_title; ?></div>
  <div class="infoBoxContent">
    <form name="tcEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&tcID=' . $tcInfo->tax_class_id . '&action=save'); ?>" method="post">

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

    <p><?php echo TEXT_INFO_LAST_MODIFIED . ' ' . (($tcInfo->last_modified > $tcInfo->date_added) ? osC_DateTime::getShort($tcInfo->last_modified) : osC_DateTime::getShort($tcInfo->date_added)); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'tcDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_tcDelete" <?php if ($_GET['action'] != 'tcDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $tcInfo->tax_class_title; ?></div>
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

    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&tcID=' . $tcInfo->tax_class_id . '&action=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'tcDefault\');" class="operationButton">'; ?></p>

<?php
    }
?>

  </div>
</div>

<?php
  }
?>
