<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_DirectoryListing = new osC_DirectoryListing(DIR_FS_BACKUP);
  $osC_DirectoryListing->setIncludeDirectories(false);
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<p align="right"><?php echo '<input type="button" value="' . IMAGE_BACKUP . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=backup') . '\';" class="infoBoxButton" />&nbsp;<input type="button" value="' . IMAGE_RESTORE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=restoreLocal') . '\';" class="infoBoxButton" />'; ?></p>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo TABLE_HEADING_TITLE; ?></th>
      <th><?php echo TABLE_HEADING_FILE_DATE; ?></th>
      <th><?php echo TABLE_HEADING_FILE_SIZE; ?></th>
      <th width="150"><?php echo TABLE_HEADING_ACTION; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="4"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . IMAGE_DELETE . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  foreach ( $osC_DirectoryListing->getFiles() as $file ) {
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&file=' . $file['name'] . '&action=download'), osc_icon('save.png', ICON_FILE_DOWNLOAD) . '&nbsp;' . $file['name']); ?></td>
      <td><?php echo osC_DateTime::getShort(osC_DateTime::fromUnixTimestamp(filemtime(DIR_FS_BACKUP . $file['name'])), true); ?></td>
      <td><?php echo number_format(filesize(DIR_FS_BACKUP . $file['name'])); ?> bytes</td>
      <td align="right">

<?php
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&file=' . $file['name'] . '&action=restore'), osc_icon('tape.png', IMAGE_RESTORE)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&file=' . $file['name'] . '&action=delete'), osc_icon('trash.png', IMAGE_DELETE));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $file['name'], null, 'id="batch' . addslashes($file['name']) . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . TEXT_LEGEND . '</b> ' . osc_icon('save.png', ICON_FILE_DOWNLOAD) . '&nbsp;' . ICON_FILE_DOWNLOAD . '&nbsp;&nbsp;' . osc_icon('tape.png', IMAGE_RESTORE) . '&nbsp;' . IMAGE_RESTORE .  '&nbsp;&nbsp;' . osc_icon('trash.png', IMAGE_DELETE) . '&nbsp;' . IMAGE_DELETE; ?></td>
  </tr>
</table>

<p><?php echo TEXT_BACKUP_DIRECTORY . ' ' . DIR_FS_BACKUP; ?></p>

<?php
  if ( defined('DB_LAST_RESTORE') ) {
?>

<p><?php echo TEXT_LAST_RESTORATION . ' ' . DB_LAST_RESTORE . ' ' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=forget'), TEXT_FORGET); ?></p>

<?php
  }
?>
