<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $goto_array = array(array('id' => '',
                            'text' => '--TOP--'));

  if ( $_SESSION['fm_directory'] != OSC_ADMIN_FILE_MANAGER_ROOT_PATH ) {
    $path_array = explode('/', substr($_SESSION['fm_directory'], strlen(OSC_ADMIN_FILE_MANAGER_ROOT_PATH)+1));

    foreach ( $path_array as $value ) {
      if ( sizeof($goto_array) < 2 ) {
        $goto_array[] = array('id' => $value,
                              'text' => $value);
      } else {
        $parent = end($goto_array);
        $goto_array[] = array('id' => $parent['id'] . '/' . $value,
                              'text' => $parent['id'] . '/' . $value);
      }
    }
  }

  $osC_DirectoryListing = new osC_DirectoryListing($_SESSION['fm_directory']);
  $osC_DirectoryListing->setStats(true);
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div style="float: right;">
  <form name="file_manager" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT); ?>" method="get"><?php echo osc_draw_hidden_field($osC_Template->getModule()); ?>

  <?php echo 'Path: ' . osc_draw_pull_down_menu('goto', $goto_array, substr($_SESSION['fm_directory'], strlen(OSC_ADMIN_FILE_MANAGER_ROOT_PATH)+1), 'onchange="this.form.submit();"'); ?>

  <?php echo '<input type="button" value="' . IMAGE_UPLOAD . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=upload') . '\';" class="infoBoxButton" />&nbsp;<input type="button" value="' . IMAGE_NEW_FILE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=save') . '\';" class="infoBoxButton" />&nbsp;<input type="button" value="' . IMAGE_NEW_FOLDER . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=saveDirectory') . '\';" class="infoBoxButton" />'; ?>

  </form>
</div>

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
      <th width="150"><?php echo TABLE_HEADING_ACTION; ?></th>
    </tr>
  </thead>
  <tbody>

<?php
  if ( $_SESSION['fm_directory'] != OSC_ADMIN_FILE_MANAGER_ROOT_PATH ) {
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td colspan="8"><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&goto=' . $goto_array[sizeof($goto_array)-2]['id']), osc_icon('2uparrow.png') . '&nbsp;--Parent--'); ?></td>
    </tr>

<?php
  }

  foreach ( $osC_DirectoryListing->getFiles() as $file ) {
    $file_owner = posix_getpwuid($file['user_id']);
    $group_owner = posix_getgrgid($file['group_id']);

    if ( $file['is_directory'] === true ) {
      $entry_icon = osc_icon('folder_red.png');
      $entry_url = osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&directory=' . $file['name']);
    } else {
      $entry_icon = osc_icon('file.png', ICON_FILE);
      $entry_url = osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&entry=' . $file['name'] . '&action=save');
    }
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php echo osc_link_object($entry_url, $entry_icon . '&nbsp;' . $file['name']); ?></td>
      <td align="right"><?php echo number_format($file['size']); ?></td>
      <td align="center"><tt><?php echo osc_get_file_permissions($file['permissions']); ?></tt></td>
      <td><?php echo $file_owner['name']; ?></td>
      <td><?php echo $group_owner['name']; ?></td>
      <td align="center"><?php echo osc_icon(is_writable($osC_DirectoryListing->getDirectory() . '/' . $file['name']) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null); ?></td>
      <td align="right"><?php echo date('F d Y H:i:s', $file['last_modified']); ?></td>
      <td align="right">

<?php
    if ( $file['is_directory'] === false ) {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&entry=' . $file['name'] . '&action=save'), osc_icon('edit.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&entry=' . $file['name'] . '&action=download'), osc_icon('save.png', IMAGE_SAVE)) . '&nbsp;';
    } else {
      echo osc_image('images/pixel_trans.gif') . '&nbsp;' .
           osc_image('images/pixel_trans.gif') . '&nbsp;';
    }

    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&entry=' . $file['name'] . '&action=delete'), osc_icon('trash.png', IMAGE_DELETE));
?>

      </td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

<p><?php echo osc_output_string_protected($_SESSION['fm_directory']); ?></p>
