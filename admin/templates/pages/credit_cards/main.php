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

<div id="infoBox_ccDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'ccNew\');" class="infoBoxButton">'; ?></p>

<?php
  $Qcc = $osC_Database->query('select id, credit_card_name, pattern, credit_card_status, sort_order from :table_credit_cards order by sort_order, credit_card_name');
  $Qcc->bindTable(':table_credit_cards', TABLE_CREDIT_CARDS);
  $Qcc->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qcc->execute();
?>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td><?php echo $Qcc->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_ENTRIES); ?></td>
      <td align="right"><?php echo $Qcc->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
    </tr>
  </table>

  <form name="batch" action="#" method="post">

  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_CREDIT_CARD_NAME; ?></th>
        <th><?php echo TABLE_HEADING_SORT_ORDER; ?></th>
        <th><?php echo TABLE_HEADING_STATUS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
        <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th align="right" colspan="4"><?php echo '<input type="image" src="' . osc_icon_raw('configure.png') . '" title="' . IMAGE_EDIT . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchSave') . '\';" />&nbsp;<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . IMAGE_DELETE . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />'; ?></th>
        <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
      </tr>
    </tfoot>
    <tbody>

<?php
  while ($Qcc->next()) {
    if (!isset($ccInfo) && (!isset($_GET['ccID']) || (isset($_GET['ccID']) && ($_GET['ccID'] == $Qcc->valueInt('id')))) && ($_GET['action'] != 'ccNew')) {
      $ccInfo = new objectInfo($Qcc->toArray());
    }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td onclick="document.getElementById('batch<?php echo $Qcc->valueInt('id'); ?>').checked = !document.getElementById('batch<?php echo $Qcc->valueInt('id'); ?>').checked;"><?php echo $Qcc->valueProtected('credit_card_name'); ?></td>
        <td><?php echo $Qcc->valueInt('sort_order'); ?></td>
        <td align="center"><?php echo osc_icon(($Qcc->valueInt('credit_card_status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null); ?></td>
        <td align="right">

<?php
    if (isset($ccInfo) && ($Qcc->valueInt('id') == $ccInfo->id)) {
      echo osc_link_object('#', osc_icon('configure.png', IMAGE_EDIT), 'onclick="toggleInfoBox(\'ccEdit\');"') . '&nbsp;' .
           osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'ccDelete\');"');
    } else {
      echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&ccID=' . $Qcc->valueInt('id') . '&action=ccEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&ccID=' . $Qcc->valueInt('id') . '&action=ccDelete'), osc_icon('trash.png', IMAGE_DELETE));
    }
?>

        </td>
        <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qcc->valueInt('id'), null, 'id="batch' . $Qcc->valueInt('id') . '"'); ?></td>
      </tr>

<?php
  }
?>

    </tbody>
  </table>

  </form>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td align="right"><?php echo $Qcc->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
    </tr>
  </table>
</div>

<div id="infoBox_ccNew" <?php if ($_GET['action'] != 'ccNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_HEADING_NEW_CREDIT_CARD; ?></div>
  <div class="infoBoxContent">
    <form name="ccNew" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=save'); ?>" method="post">

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_CREDIT_CARD_NAME . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('credit_card_name', null, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_CREDIT_CARD_PATTERN . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('pattern', null, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_SORT_ORDER . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('sort_order', null, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_STATUS . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_checkbox_field('credit_card_status', '1'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'ccDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($ccInfo)) {
?>

<div id="infoBox_ccEdit" <?php if ($_GET['action'] != 'ccEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $ccInfo->credit_card_name; ?></div>
  <div class="infoBoxContent">
    <form name="ccEdit" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&ccID=' . $ccInfo->id . '&action=save'); ?>" method="post">

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_CREDIT_CARD_NAME . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('credit_card_name', $ccInfo->credit_card_name, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_CREDIT_CARD_PATTERN . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('pattern', $ccInfo->pattern, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_SORT_ORDER . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('sort_order', $ccInfo->sort_order, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_STATUS . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_checkbox_field('credit_card_status', '1', $ccInfo->credit_card_status); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'ccDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_ccDelete" <?php if ($_GET['action'] != 'ccDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $ccInfo->credit_card_name; ?></div>
  <div class="infoBoxContent">
    <p><?php echo TEXT_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $ccInfo->credit_card_name . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&ccID=' . $ccInfo->id . '&action=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'ccDefault\');" class="operationButton">'; ?></p>
  </div>
</div>

<?php
  }
?>
