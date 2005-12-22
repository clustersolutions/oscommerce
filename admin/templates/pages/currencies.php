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

<div id="infoBox_cDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_CURRENCY_NAME; ?></th>
        <th><?php echo TABLE_HEADING_CURRENCY_CODES; ?></th>
        <th><?php echo TABLE_HEADING_CURRENCY_VALUE; ?></th>
        <th><?php echo TABLE_HEADING_CURRENCY_EXAMPLE; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qcurrencies = $osC_Database->query('select currencies_id, title, code, symbol_left, symbol_right, decimal_places, last_updated, value from :table_currencies order by title');
  $Qcurrencies->bindTable(':table_currencies', TABLE_CURRENCIES);
  $Qcurrencies->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qcurrencies->execute();

  while ($Qcurrencies->next()) {
    if (!isset($cInfo) && (!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $Qcurrencies->valueInt('currencies_id'))))) {
      $cInfo = new objectInfo($Qcurrencies->toArray());
    }

    if (isset($cInfo) && ($Qcurrencies->valueInt('currencies_id') == $cInfo->currencies_id) ) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . tep_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $Qcurrencies->valueInt('currencies_id')) . '\';">' . "\n";
    }
?>
        <td>
<?php
    if (DEFAULT_CURRENCY == $Qcurrencies->value('code')) {
      echo '<b>' . $Qcurrencies->value('title') . ' (' . TEXT_DEFAULT . ')</b>';
    } else {
      echo $Qcurrencies->value('title');
    }
?>
        </td>
        <td><?php echo $Qcurrencies->value('code'); ?></td>
        <td><?php echo number_format($Qcurrencies->valueDecimal('value'), 8); ?></td>
        <td><?php echo $osC_Currencies->format(1499.99, $Qcurrencies->value('code'), 1); ?></td>
        <td align="right">
<?php
    if (isset($cInfo) && ($Qcurrencies->valueInt('currencies_id') == $cInfo->currencies_id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'cEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="#" onclick="toggleInfoBox(\'cDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $Qcurrencies->valueInt('currencies_id') . '&action=cEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="' . tep_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $Qcurrencies->valueInt('currencies_id') . '&action=cDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
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
      <td class="smallText"><?php echo $Qcurrencies->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_CURRENCIES); ?></td>
      <td class="smallText" align="right"><?php echo $Qcurrencies->displayBatchLinksPullDown(); ?></td>
    </tr>
  </table>

  <p align="right">
<?php
  if (isset($cInfo)) {
    echo '<input type="button" value="' . IMAGE_UPDATE_CURRENCIES . '" onclick="toggleInfoBox(\'cUpdate\');" class="infoBoxButton">&nbsp;';
  }

  echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'cNew\');" class="infoBoxButton">';
?>
  </p>
</div>

<div id="infoBox_cNew" <?php if ($action != 'cNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_INFO_HEADING_NEW_CURRENCY; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('cNew', FILENAME_CURRENCIES, 'action=save'); ?>

    <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CURRENCY_TITLE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('title', '', 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CURRENCY_CODE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('code', '', 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CURRENCY_SYMBOL_LEFT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('symbol_left', '', 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CURRENCY_SYMBOL_RIGHT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('symbol_right', '', 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CURRENCY_DECIMAL_PLACES . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('decimal_places', '', 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CURRENCY_VALUE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('value', '', 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_SET_AS_DEFAULT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('default', '', 'style="width: 100%;"'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($cInfo)) {
?>

<div id="infoBox_cUpdate" <?php if ($action != 'cUpdate') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/reload.png', IMAGE_UPDATE, '16', '16') . ' ' . IMAGE_UPDATE_CURRENCIES; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('cUpdate', FILENAME_CURRENCIES, 'action=update_currencies'); ?>

    <p><?php echo TEXT_INFO_UPDATE_SERVICE_INTRO; ?></p>

    <p>
<?php
    $services = array(array('id' => 'oanda', 'text' => 'Oanda (http://www.oanda.com)'),
                      array('id' => 'xe', 'text' => 'XE (http://www.xe.com)'));

    echo osc_draw_radio_field('service', $services, '', '', false, '<br />');
?>
    </p>

    <p><?php echo TEXT_INFO_SERVICE_TERMS; ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_UPDATE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_cEdit" <?php if ($action != 'cEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $cInfo->title; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('cEdit', FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=save'); ?>

    <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CURRENCY_TITLE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('title', $cInfo->title, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CURRENCY_CODE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('code', $cInfo->code, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CURRENCY_SYMBOL_LEFT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('symbol_left', $cInfo->symbol_left, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CURRENCY_SYMBOL_RIGHT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('symbol_right', $cInfo->symbol_right, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CURRENCY_DECIMAL_PLACES . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('decimal_places', $cInfo->decimal_places, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_CURRENCY_VALUE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('value', $cInfo->value, 'style="width: 100%;"'); ?></td>
      </tr>
<?php
    if (DEFAULT_CURRENCY != $cInfo->code) {
?>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_SET_AS_DEFAULT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('default', '', 'style="width: 100%;"'); ?></td>
      </tr>
<?php
    }
?>
    </table>

    <p align="center"><?php echo ((DEFAULT_CURRENCY == $cInfo->code) ? osc_draw_hidden_field('is_default', 'true') : '') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_cDelete" <?php if ($action != 'cDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $cInfo->title; ?></div>
  <div class="infoBoxContent">
<?php
    if (DEFAULT_CURRENCY == $cInfo->code) {
?>
    <p><?php echo '<b>' . TEXT_INFO_DELETE_PROHIBITED . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>
<?php
    } else {
?>
    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $cInfo->title . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>
<?php
    }
?>
  </div>
</div>

<?php
  }
?>
