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

<div id="infoBox_lDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_LANGUAGE_NAME; ?></th>
        <th><?php echo TABLE_HEADING_LANGUAGE_CODE; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qlanguages = $osC_Database->query('select * from :table_languages order by sort_order, name');
  $Qlanguages->bindTable(':table_languages', TABLE_LANGUAGES);
  $Qlanguages->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qlanguages->execute();

  while ($Qlanguages->next()) {
    if (!isset($lInfo) && (!isset($_GET['lID']) || (isset($_GET['lID']) && ($_GET['lID'] == $Qlanguages->valueInt('languages_id'))))) {
      $lInfo = new objectInfo($Qlanguages->toArray());
    }

    if (isset($lInfo) && ($Qlanguages->valueInt('languages_id') == $lInfo->languages_id)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . tep_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $Qlanguages->valueInt('languages_id')) . '\';">' . "\n";
    }

    if ($Qlanguages->value('code') == DEFAULT_LANGUAGE) {
      echo '        <td><b>' . $Qlanguages->value('name') . ' (' . TEXT_DEFAULT . ')</b></td>' . "\n";
    } else {
      echo '        <td>' . $Qlanguages->value('name') . '</td>' . "\n";
    }
?>
        <td><?php echo $Qlanguages->value('code'); ?></td>
        <td align="right">
<?php
    if (isset($lInfo) && ($Qlanguages->valueInt('languages_id') == $lInfo->languages_id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'lEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="#" onclick="toggleInfoBox(\'lDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $Qlanguages->valueInt('languages_id') . '&action=lEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="' . tep_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $Qlanguages->valueInt('languages_id') . '&action=lDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
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
      <td class="smallText"><?php echo $Qlanguages->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_LANGUAGES); ?></td>
      <td class="smallText" align="right"><?php echo $Qlanguages->displayBatchLinksPullDown(); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'lNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_lNew" <?php if ($action != 'lNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_INFO_HEADING_NEW_LANGUAGE; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('lNew', FILENAME_LANGUAGES, 'action=save'); ?>

    <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('name', '', 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_CODE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('code', '', 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_IMAGE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('image', '', 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DIRECTORY . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('directory', '', 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_SORT_ORDER . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('sort_order', '', 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_SET_DEFAULT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($lInfo)) {
?>

<div id="infoBox_lEdit" <?php if ($action != 'lEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $lInfo->name; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('lEdit', FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=save'); ?>

    <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('name', $lInfo->name, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_CODE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('code', $lInfo->code, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_IMAGE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('image', $lInfo->image, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DIRECTORY . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('directory', $lInfo->directory, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_SORT_ORDER . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('sort_order', $lInfo->sort_order, 'style="width: 100%"'); ?></td>
      </tr>
<?php
    if (DEFAULT_LANGUAGE != $lInfo->code) {
?>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_SET_DEFAULT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
      </tr>
<?php
    }
?>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_lDelete" <?php if ($action != 'lDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $lInfo->name; ?></div>
  <div class="infoBoxContent">
<?php
    if (DEFAULT_LANGUAGE == $lInfo->code) {
?>
    <p><?php echo '<b>' . TEXT_INFO_DELETE_PROHIBITED . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>
<?php
    } else {
?>
    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $lInfo->name . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>
<?php
    }
?>
  </div>
</div>

<?php
  }
?>
