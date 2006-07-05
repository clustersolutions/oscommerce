<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_gDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_IMAGE_GROUPS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qgroups = $osC_Database->query('select * from :table_products_images_groups where language_id = :language_id order by title');
  $Qgroups->bindTable(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
  $Qgroups->bindInt(':language_id', $osC_Language->getID());
  $Qgroups->execute();

  while ($Qgroups->next()) {
    if (!isset($gInfo) && (!isset($_GET['gID']) || (isset($_GET['gID']) && ($_GET['gID'] == $Qgroups->valueInt('id'))))) {
      $gInfo = new objectInfo($Qgroups->toArray());
    }

    if (isset($gInfo) && ($Qgroups->valueInt('id') == $gInfo->id)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . tep_href_link(FILENAME_IMAGE_GROUPS, '&gID=' . $Qgroups->valueInt('id')) . '\';">' . "\n";
    }

    if (DEFAULT_IMAGE_GROUP_ID == $Qgroups->valueInt('id')) {
      echo '        <td><b>' . $Qgroups->value('title') . ' (' . TEXT_DEFAULT . ')</b></td>' . "\n";
    } else {
      echo '        <td>' . $Qgroups->value('title') . '</td>' . "\n";
    }
?>
        <td align="right">
<?php
    if (isset($gInfo) && ($Qgroups->valueInt('id') == $gInfo->id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'gEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="#" onclick="toggleInfoBox(\'gDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_IMAGE_GROUPS, 'gID=' . $Qgroups->valueInt('id') . '&action=gEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="' . tep_href_link(FILENAME_IMAGE_GROUPS, 'gID=' . $Qgroups->valueInt('id') . '&action=gDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    }
?>
        </td>
      </tr>
<?php
  }
?>
    </tbody>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'gNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_gNew" <?php if ($action != 'gNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_INFO_HEADING_NEW_IMAGE_GROUP; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('gNew', FILENAME_IMAGE_GROUPS, 'action=save'); ?>

    <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_IMAGE_GROUP_TITLE . '</b>'; ?></td>
        <td class="smallText" width="60%">
<?php
  foreach ($osC_Language->getAll() as $l) {
    echo tep_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' . osc_draw_input_field('title[' . $l['id'] . ']') . '<br />';
  }
?>
        </td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_IMAGE_GROUP_CODE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('code'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_IMAGE_GROUP_WIDTH . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('width'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_IMAGE_GROUP_HEIGHT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('height'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_IMAGE_GROUP_FORCE_SIZE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('force_size', 1); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_SET_DEFAULT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'gDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($gInfo)) {
?>

<div id="infoBox_gEdit" <?php if ($action != 'gEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $gInfo->title; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('gEdit', FILENAME_IMAGE_GROUPS, 'gID=' . $gInfo->id . '&action=save'); ?>

    <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_IMAGE_GROUP_TITLE . '</b>'; ?></td>
        <td class="smallText" width="60%">

<?php
    $status_name = array();

    $Qgd = $osC_Database->query('select language_id, title from :table_products_images_groups where id = :id');
    $Qgd->bindTable(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
    $Qgd->bindInt(':id', $gInfo->id);
    $Qgd->execute();

    while ($Qgd->next()) {
      $status_name[$Qgd->valueInt('language_id')] = $Qgd->value('title');
    }

    foreach ($osC_Language->getAll() as $l) {
      echo tep_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' . osc_draw_input_field('title[' . $l['id'] . ']', (isset($status_name[$l['id']]) ? $status_name[$l['id']] : '')) . '<br />';
    }
?>

        </td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_IMAGE_GROUP_CODE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('code', $gInfo->code); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_IMAGE_GROUP_WIDTH . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('width', $gInfo->size_width); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_IMAGE_GROUP_HEIGHT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('height', $gInfo->size_height); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_IMAGE_GROUP_FORCE_SIZE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('force_size', 1, $gInfo->force_size); ?></td>
      </tr>

<?php
    if (DEFAULT_IMAGE_GROUP_ID != $gInfo->id) {
?>

      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_SET_DEFAULT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
      </tr>

<?php
    }
?>

    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'gDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_gDelete" <?php if ($action != 'gDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $gInfo->title; ?></div>
  <div class="infoBoxContent">

<?php
    if (DEFAULT_IMAGE_GROUP_ID == $gInfo->id) {
?>

    <p><?php echo '<b>' . TEXT_INFO_DELETE_PROHIBITED . '</b>'; ?></p>

    <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="toggleInfoBox(\'gDefault\');" class="operationButton">'; ?></p>

<?php
    } else {
?>

    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>

    <p><?php echo '<b>' . $gInfo->title . '</b>'; ?></p>

    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_IMAGE_GROUPS, 'gID=' . $gInfo->id . '&action=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'gDefault\');" class="operationButton">'; ?></p>

<?php
    }
?>

  </div>
</div>

<?php
  }
?>
