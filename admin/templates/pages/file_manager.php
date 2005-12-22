<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/directory_listing.php');

  $osC_DirectoryListing = new osC_DirectoryListing($current_path);
  $osC_DirectoryListing->setStats(true);
  $files = $osC_DirectoryListing->getFiles();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><h1><?php echo HEADING_TITLE; ?></h1></td>
    <td class="smallText" align="right">
<?php
  echo tep_draw_form('file_manager', FILENAME_FILE_MANAGER, '', 'get') .
       osc_draw_pull_down_menu('goto', $goto_array, substr($current_path, strlen(OSC_ADMIN_FILE_MANAGER_ROOT_PATH)+1), 'onchange="this.form.submit();"') .
       '</form>';
?>
    </td>
  </tr>
</table>

<div id="infoBox_fmDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_FILENAME; ?></th>
        <th><?php echo TABLE_HEADING_SIZE; ?></th>
        <th><?php echo TABLE_HEADING_PERMISSIONS; ?></th>
        <th><?php echo TABLE_HEADING_USER; ?></th>
        <th><?php echo TABLE_HEADING_GROUP; ?></th>
        <th><?php echo TABLE_HEADING_WRITEABLE; ?></th>
        <th><?php echo TABLE_HEADING_LAST_MODIFIED; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  if ($current_path != OSC_ADMIN_FILE_MANAGER_ROOT_PATH) {
    echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' . "\n" .
         '        <td colspan="8"><a href="' . tep_href_link(FILENAME_FILE_MANAGER, 'goto=' . $goto_array[sizeof($goto_array)-2]['id']) . '">' . tep_image('templates/' . $template . '/images/icons/16x16/2uparrow.png', '', '16', '16') . '&nbsp;--Parent--</a></td>' . "\n" .
         '      </tr>' . "\n";
  }

  for ($i=0, $n=sizeof($files); $i<$n; $i++) {
    if (!isset($fmInfo) && (!isset($_GET['entry']) || (isset($_GET['entry']) && ($_GET['entry'] == $files[$i]['name'])))) {
      $fmInfo = new objectInfo($files[$i]);
    }

    $file_owner = posix_getpwuid($files[$i]['user_id']);
    $group_owner = posix_getgrgid($files[$i]['group_id']);

    if ($files[$i]['is_directory'] === true) {
      $entry_icon = tep_image('templates/' . $template . '/images/icons/16x16/folder_red.png', '', '16', '16');
      $entry_url = tep_href_link(FILENAME_FILE_MANAGER, 'directory=' . $files[$i]['name']);
    } else {
      $entry_icon = tep_image('templates/' . $template . '/images/icons/16x16/file.png', ICON_FILE, '16', '16');
      $entry_url = tep_href_link(FILENAME_FILE_MANAGER, 'entry=' . $files[$i]['name'] . '&action=fmEdit');
    }

    if (isset($fmInfo) && ($files[$i]['name'] == $fmInfo->name)) {
        echo '      <tr class="selected">' . "\n";
      } else {
        echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . tep_href_link(FILENAME_FILE_MANAGER, 'entry=' . $files[$i]['name']) . '\';">' . "\n";
      }
?>
        <td><?php echo '<a href="' . $entry_url . '">' . $entry_icon . '&nbsp;' . $files[$i]['name'] . '</a>'; ?></td>
        <td align="right"><?php echo number_format($files[$i]['size']); ?></td>
        <td align="center"><tt><?php echo tep_get_file_permissions($files[$i]['permissions']); ?></tt></td>
        <td><?php echo $file_owner['name']; ?></td>
        <td><?php echo $group_owner['name']; ?></td>
        <td align="center"><?php echo tep_image('templates/' . $template . '/images/icons/' . (is_writable($osC_DirectoryListing->getDirectory() . '/' . $files[$i]['name']) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif')); ?></td>
        <td align="right"><?php echo date('F d Y H:i:s', $files[$i]['last_modified']); ?></td>
        <td align="right">
<?php
    if ($files[$i]['is_directory'] === false) {
      echo '<a href="#" onclick="document.location.href=\'' . tep_href_link(FILENAME_FILE_MANAGER, 'entry=' . $files[$i]['name'] . '&action=fmEdit') . '\';">' . tep_image('templates/' . $template . '/images/icons/16x16/edit.png', IMAGE_EDIT, '16', '16') . '</a>' . '&nbsp;' .
           '<a href="#" onclick="document.location.href=\'' . tep_href_link(FILENAME_FILE_MANAGER, 'entry=' . $files[$i]['name'] . '&action=download') . '\';">' . tep_image('templates/' . $template . '/images/icons/16x16/save.png', IMAGE_SAVE, '16', '16') . '</a>' . '&nbsp;';
    } else {
      echo tep_image('images/pixel_trans.gif') . '&nbsp;' .
           tep_image('images/pixel_trans.gif') . '&nbsp;';
    }

    if (isset($fmInfo) && ($files[$i]['name'] == $fmInfo->name)) {
      echo '<a href="#" onclick="toggleInfoBox(\'fmDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="#" onclick="document.location.href=\'' . tep_href_link(FILENAME_FILE_MANAGER, 'entry=' . $files[$i]['name'] . '&action=fmDelete') . '\';">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    }
?>
        </td>
      </tr>
<?php
  }
?>
    </tbody>
  </table>

  <p><?php echo $current_path; ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr valign="top">
<?php
  if ($current_path != OSC_ADMIN_FILE_MANAGER_ROOT_PATH) {
    echo '      <td class="smallText"><input type="button" value="' . IMAGE_RESET . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_FILE_MANAGER, 'action=reset') . '\';" class="infoBoxButton"></td>' . "\n";
  }
?>
      <td class="smallText" align="right"><?php echo '<input type="button" value="' . IMAGE_UPLOAD . '" onclick="toggleInfoBox(\'fmUpload\');" class="infoBoxButton">&nbsp;<input type="button" value="' . IMAGE_NEW_FILE . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_FILE_MANAGER, 'action=fmEdit') . '\';" class="infoBoxButton">&nbsp;<input type="button" value="' . IMAGE_NEW_FOLDER . '" onclick="toggleInfoBox(\'fmNewDirectory\');" class="infoBoxButton">'; ?></td>
    </tr>
  </table>
</div>

<div id="infoBox_fmNewDirectory" <?php if ($action != 'fmNewDirectory') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_NEW_FOLDER; ?></div>
  <div class="infoBoxContent">

<?php
  if (is_writeable($current_path)) {
?>

    <?php echo tep_draw_form('fmNewDirectory', FILENAME_FILE_MANAGER, 'action=new_directory'); ?>

    <p><?php echo TEXT_NEW_FOLDER_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_FILE_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('directory_name', '', 'style="width: 100%;"'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'fmDefault\');" class="operationButton">'; ?></p>

    </form>

<?php
  } else {
?>

    <p><?php echo sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, $current_path); ?></p>

    <p align="center"><?php echo '<input type="button" value="Retry" onclick="document.location.href=\'' . tep_href_link(FILENAME_FILE_MANAGER, 'action=fmNewDirectory') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'fmDefault\');" class="operationButton">'; ?></p>

<?php
  }
?>

  </div>
</div>

<div id="infoBox_fmUpload" <?php if ($action != 'fmUpload') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_INFO_HEADING_UPLOAD; ?></div>
  <div class="infoBoxContent">

<?php
  if (is_writeable($current_path)) {
?>

    <?php echo tep_draw_form('fmUpload', FILENAME_FILE_MANAGER, 'action=processuploads', 'post', 'enctype="multipart/form-data"'); ?>

    <p><?php echo TEXT_UPLOAD_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
    for ($i=0; $i<10; $i++) {
?>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_FILE_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_file_field('file_' . $i); ?></td>
      </tr>
<?php
    }
?>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_UPLOAD . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'fmDefault\');" class="operationButton">'; ?></p>

    </form>

<?php
  } else {
?>

    <p><?php echo sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, $current_path); ?></p>

    <p align="center"><?php echo '<input type="button" value="Retry" onclick="document.location.href=\'' . tep_href_link(FILENAME_FILE_MANAGER, 'action=fmUpload') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'fmDefault\');" class="operationButton">'; ?></p>

<?php
  }
?>

  </div>
</div>

<?php
  if (isset($fmInfo)) {
?>

<div id="infoBox_fmDelete" <?php if ($action != 'fmDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $fmInfo->name; ?></div>
  <div class="infoBoxContent">

<?php
    if (is_writeable($current_path . '/' . $fmInfo->name)) {
?>

    <p><?php echo TEXT_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $current_path . '/' . $fmInfo->name . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_FILE_MANAGER, 'entry=' . $fmInfo->name . '&action=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'fmDefault\');" class="operationButton">'; ?></p>

<?php
    } else {
?>

    <p><?php echo sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, $current_path . '/' . $fmInfo->name); ?></p>

    <p align="center"><?php echo '<input type="button" value="Retry" onclick="document.location.href=\'' . tep_href_link(FILENAME_FILE_MANAGER, 'entry=' . $fmInfo->name . '&action=fmDelete') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'fmDefault\');" class="operationButton">'; ?></p>

<?php
    }
?>

  </div>
</div>

<?php
  }
?>
