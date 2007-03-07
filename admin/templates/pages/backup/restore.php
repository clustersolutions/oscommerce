<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  switch ( substr($_GET['file'], -3) ) {
    case 'zip':
      $file_compression = 'ZIP';

      break;

    case '.gz':
      $file_compression = 'GZIP';

      break;

    default:
      $file_compression = TEXT_NO_EXTENSION;

      break;
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . $_GET['file']; ?></div>
<div class="infoBoxContent">
  <form name="bRestoreLocal" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&file=' . $_GET['file'] . '&action=restore'); ?>" method="post" enctype="multipart/form-data">

  <p><?php echo sprintf(TEXT_INFO_RESTORE, DIR_FS_BACKUP . (($file_compression != TEXT_NO_EXTENSION) ? substr($_GET['file'], 0, strrpos($_GET['file'], '.')) : $_GET['file']), ($file_compression != TEXT_NO_EXTENSION) ? TEXT_INFO_UNPACK : ''); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_RESTORE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
