<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $compression_array = array(array('id' => 'none',
                                   'text' => $osC_Language->get('field_compression_none')));

  if ( !osc_empty(LOCAL_EXE_GZIP) && file_exists(LOCAL_EXE_GZIP) ) {
    $compression_array[] = array('id' => 'gzip',
                                 'text' => $osC_Language->get('field_compression_gzip'));
  }

  if ( !osc_empty(LOCAL_EXE_ZIP) && file_exists(LOCAL_EXE_ZIP) ) {
    $compression_array[] = array('id' => 'zip',
                                 'text' => $osC_Language->get('field_compression_zip'));
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_backup'); ?></div>
<div class="infoBoxContent">
  <form name="bBackup" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=backup'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_new_backup'); ?></p>

  <p><?php echo osc_draw_radio_field('compression', $compression_array, 'none', null, '<br />'); ?></p>

  <p>

<?php
  if ( !osc_empty(DIR_FS_BACKUP) && is_dir(DIR_FS_BACKUP) && is_writeable(DIR_FS_BACKUP) ) {
    echo osc_draw_checkbox_field('download_only', array(array('id' => 'yes', 'text' => $osC_Language->get('field_download_only'))));
  } else {
    echo osc_draw_radio_field('download_only', array(array('id' => 'yes', 'text' => $osC_Language->get('field_download_only'))), true);
  }
?>

  </p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_backup') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
