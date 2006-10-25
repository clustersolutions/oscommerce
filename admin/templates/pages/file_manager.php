<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/directory_listing.php');

  $osC_DirectoryListing = new osC_DirectoryListing($current_path);
  $osC_DirectoryListing->setStats(true);
  $files = $osC_DirectoryListing->getFiles();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1></td>
    <td class="smallText" align="right">

<?php
  echo '<form name="file_manager" action="' . osc_href_link_admin(FILENAME_DEFAULT) . '" method="get">' . osc_draw_hidden_field($osC_Template->getModule()) .
       osc_draw_pull_down_menu('goto', $goto_array, substr($current_path, strlen(OSC_ADMIN_FILE_MANAGER_ROOT_PATH)+1), 'onchange="this.form.submit();"') .
       '</form>';
?>

    </td>
  </tr>
</table>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_fmDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
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
         '        <td colspan="8">' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&goto=' . $goto_array[sizeof($goto_array)-2]['id']), osc_icon('2uparrow.png') . '&nbsp;--Parent--') . '</td>' . "\n" .
         '      </tr>' . "\n";
  }

  for ($i=0, $n=sizeof($files); $i<$n; $i++) {
    if (!isset($fmInfo) && (!isset($_GET['entry']) || (isset($_GET['entry']) && ($_GET['entry'] == $files[$i]['name'])))) {
      $fmInfo = new objectInfo($files[$i]);
    }

    $file_owner = posix_getpwuid($files[$i]['user_id']);
    $group_owner = posix_getgrgid($files[$i]['group_id']);

    if ($files[$i]['is_directory'] === true) {
      $entry_icon = osc_icon('folder_red.png');
      $entry_url = osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&directory=' . $files[$i]['name']);
    } else {
      $entry_icon = osc_icon('file.png', ICON_FILE);
      $entry_url = osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&entry=' . $files[$i]['name'] . '&action=fmEdit');
    }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td><?php echo osc_link_object($entry_url, $entry_icon . '&nbsp;' . $files[$i]['name']); ?></td>
        <td align="right"><?php echo number_format($files[$i]['size']); ?></td>
        <td align="center"><tt><?php echo osc_get_file_permissions($files[$i]['permissions']); ?></tt></td>
        <td><?php echo $file_owner['name']; ?></td>
        <td><?php echo $group_owner['name']; ?></td>
        <td align="center"><?php echo osc_icon(is_writable($osC_DirectoryListing->getDirectory() . '/' . $files[$i]['name']) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null); ?></td>
        <td align="right"><?php echo date('F d Y H:i:s', $files[$i]['last_modified']); ?></td>
        <td align="right">

<?php
    if ($files[$i]['is_directory'] === false) {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&entry=' . $files[$i]['name'] . '&action=fmEdit'), osc_icon('edit.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&entry=' . $files[$i]['name'] . '&action=download'), osc_icon('save.png', IMAGE_SAVE)) . '&nbsp;';
    } else {
      echo osc_image('images/pixel_trans.gif') . '&nbsp;' .
           osc_image('images/pixel_trans.gif') . '&nbsp;';
    }

    if (isset($fmInfo) && ($files[$i]['name'] == $fmInfo->name)) {
      echo osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'fmDelete\');"');
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&entry=' . $files[$i]['name'] . '&action=fmDelete'), osc_icon('trash.png', IMAGE_DELETE));
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
    echo '      <td class="smallText"><input type="button" value="' . IMAGE_RESET . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=reset') . '\';" class="infoBoxButton"></td>' . "\n";
  }
?>

      <td class="smallText" align="right"><?php echo '<input type="button" value="' . IMAGE_UPLOAD . '" onclick="toggleInfoBox(\'fmUpload\');" class="infoBoxButton">&nbsp;<input type="button" value="' . IMAGE_NEW_FILE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=fmEdit') . '\';" class="infoBoxButton">&nbsp;<input type="button" value="' . IMAGE_NEW_FOLDER . '" onclick="toggleInfoBox(\'fmNewDirectory\');" class="infoBoxButton">'; ?></td>
    </tr>
  </table>
</div>

<div id="infoBox_fmNewDirectory" <?php if ($_GET['action'] != 'fmNewDirectory') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_NEW_FOLDER; ?></div>
  <div class="infoBoxContent">

<?php
  if (is_writeable($current_path)) {
?>

    <form name="fmNewDirectory" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=new_directory'); ?>" method="post">

    <p><?php echo TEXT_NEW_FOLDER_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_FILE_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('directory_name', null, 'style="width: 100%;"'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'fmDefault\');" class="operationButton">'; ?></p>

    </form>

<?php
  } else {
?>

    <p><?php echo sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, $current_path); ?></p>

    <p align="center"><?php echo '<input type="button" value="Retry" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=fmNewDirectory') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'fmDefault\');" class="operationButton">'; ?></p>

<?php
  }
?>

  </div>
</div>

<div id="infoBox_fmUpload" <?php if ($_GET['action'] != 'fmUpload') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_UPLOAD; ?></div>
  <div class="infoBoxContent">

<?php
  if (is_writeable($current_path)) {
?>

    <form name="fmUpload" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=processuploads'); ?>" method="post" enctype="multipart/form-data">

    <p><?php echo TEXT_UPLOAD_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
    for ($i=0; $i<10; $i++) {
?>

      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_FILE_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_file_field('file_' . $i, true); ?></td>
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

    <p align="center"><?php echo '<input type="button" value="Retry" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=fmUpload') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'fmDefault\');" class="operationButton">'; ?></p>

<?php
  }
?>

  </div>
</div>

<?php
  if (isset($fmInfo)) {
?>

<div id="infoBox_fmDelete" <?php if ($_GET['action'] != 'fmDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $fmInfo->name; ?></div>
  <div class="infoBoxContent">

<?php
    if (is_writeable($current_path . '/' . $fmInfo->name)) {
?>

    <p><?php echo TEXT_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $current_path . '/' . $fmInfo->name . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&entry=' . $fmInfo->name . '&action=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'fmDefault\');" class="operationButton">'; ?></p>

<?php
    } else {
?>

    <p><?php echo sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, $current_path . '/' . $fmInfo->name); ?></p>

    <p align="center"><?php echo '<input type="button" value="Retry" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&entry=' . $fmInfo->name . '&action=fmDelete') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'fmDefault\');" class="operationButton">'; ?></p>

<?php
    }
?>

  </div>
</div>

<?php
  }
?>
