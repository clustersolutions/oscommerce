<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $compression_array = array(array('id' => 'none',
                                   'text' => TEXT_INFO_USE_NO_COMPRESSION));

  if ( !osc_empty(LOCAL_EXE_GZIP) && file_exists(LOCAL_EXE_GZIP) ) {
    $compression_array[] = array('id' => 'gzip',
                                 'text' => TEXT_INFO_USE_GZIP);
  }

  if ( !osc_empty(LOCAL_EXE_ZIP) && file_exists(LOCAL_EXE_ZIP) ) {
    $compression_array[] = array('id' => 'zip',
                                 'text' => TEXT_INFO_USE_ZIP);
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_BACKUP; ?></div>
<div class="infoBoxContent">
  <form name="bBackup" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=backup'); ?>" method="post">

  <p><?php echo TEXT_INFO_NEW_BACKUP; ?></p>

  <p><?php echo osc_draw_radio_field('compression', $compression_array, 'none', null, '<br />'); ?></p>

  <p>

<?php
  if ( !osc_empty(DIR_FS_BACKUP) && is_dir(DIR_FS_BACKUP) && is_writeable(DIR_FS_BACKUP) ) {
    echo osc_draw_checkbox_field('download_only', array(array('id' => 'yes', 'text' => TEXT_INFO_DOWNLOAD_ONLY))) . '*<br /><br />*' . TEXT_INFO_BEST_THROUGH_HTTPS;
  } else {
    echo osc_draw_radio_field('download_only', array(array('id' => 'yes', 'text' => TEXT_INFO_DOWNLOAD_ONLY)), true) . '*<br /><br />*' . TEXT_INFO_BEST_THROUGH_HTTPS;
  }
?>

  </p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_BACKUP . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
