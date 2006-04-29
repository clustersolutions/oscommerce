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

<div id="infoBox_ccDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_CREDIT_CARD_NAME; ?></th>
        <th><?php echo TABLE_HEADING_SORT_ORDER; ?></th>
        <th><?php echo TABLE_HEADING_STATUS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qcc = $osC_Database->query('select id, credit_card_name, pattern, credit_card_status, sort_order from :table_credit_cards order by sort_order, credit_card_name');
  $Qcc->bindTable(':table_credit_cards', TABLE_CREDIT_CARDS);
  $Qcc->execute();

  while ($Qcc->next()) {
    if (!isset($ccInfo) && (!isset($_GET['ccID']) || (isset($_GET['ccID']) && ($_GET['ccID'] == $Qcc->valueInt('id')))) && ($action != 'ccNew')) {
      $ccInfo = new objectInfo($Qcc->toArray());
    }

    if (isset($ccInfo) && ($Qcc->valueInt('id') == $ccInfo->id)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . tep_href_link(FILENAME_CREDIT_CARDS, 'page=' . $_GET['page'] . '&ccID=' . $Qcc->valueInt('id')) . '\';">' . "\n";
    }
?>
        <td><?php echo $Qcc->valueProtected('credit_card_name'); ?></td>
        <td><?php echo $Qcc->valueInt('sort_order'); ?></td>
        <td align="center"><?php echo tep_image('templates/' . $template . '/images/icons/' . (($Qcc->valueInt('credit_card_status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif')); ?></td>
        <td align="right">
<?php
    if (isset($ccInfo) && ($Qcc->valueInt('id') == $ccInfo->id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'ccEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="#" onclick="toggleInfoBox(\'ccDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_CREDIT_CARDS, 'page=' . $_GET['page'] . '&ccID=' . $Qcc->valueInt('id') . '&action=ccEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="' . tep_href_link(FILENAME_CREDIT_CARDS, 'page=' . $_GET['page'] . '&ccID=' . $Qcc->valueInt('id') . '&action=ccDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    }
?>
        </td>
      </tr>
<?php
  }
?>
    </tbody>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'ccNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_ccNew" <?php if ($action != 'ccNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_HEADING_NEW_CREDIT_CARD; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('ccNew', FILENAME_CREDIT_CARDS, 'action=save'); ?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_CREDIT_CARD_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('credit_card_name', '', 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_CREDIT_CARD_PATTERN . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('pattern', '', 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_SORT_ORDER . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('sort_order', '', 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_STATUS . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('credit_card_status', '1'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'ccDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($ccInfo)) {
?>

<div id="infoBox_ccEdit" <?php if ($action != 'ccEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $ccInfo->credit_card_name; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('ccEdit', FILENAME_CREDIT_CARDS, 'page=' . $_GET['page'] . '&ccID=' . $ccInfo->id . '&action=save'); ?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_CREDIT_CARD_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('credit_card_name', $ccInfo->credit_card_name, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_CREDIT_CARD_PATTERN . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('pattern', $ccInfo->pattern, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_SORT_ORDER . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('sort_order', $ccInfo->sort_order, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_STATUS . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('credit_card_status', '1', $ccInfo->credit_card_status); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'ccDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_ccDelete" <?php if ($action != 'ccDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $ccInfo->credit_card_name; ?></div>
  <div class="infoBoxContent">
    <p><?php echo TEXT_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $ccInfo->credit_card_name . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_CREDIT_CARDS, 'page=' . $_GET['page'] . '&ccID=' . $ccInfo->id . '&action=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'ccDefault\');" class="operationButton">'; ?></p>
  </div>
</div>

<?php
  }
?>
