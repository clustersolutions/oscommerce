<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_UPLOAD . ': ' . osc_output_string_protected($_SESSION['fm_directory']); ?></div>
<div class="infoBoxContent">

<?php
  if ( is_writeable($_SESSION['fm_directory']) ) {
?>

  <form name="fmUpload" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=upload'); ?>" method="post" enctype="multipart/form-data">

  <p><?php echo TEXT_UPLOAD_INTRO; ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
   for ( $i = 0; $i < 10; $i++ ) {
?>

    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_FILE_NAME . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_file_field('file_' . $i, true); ?></td>
    </tr>

<?php
  }
?>

  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_UPLOAD . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>

<?php
  } else {
?>

  <p><?php echo sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, $_SESSION['fm_directory']); ?></p>

  <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

<?php
  }
?>

</div>
