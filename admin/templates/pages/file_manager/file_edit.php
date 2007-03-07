<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $writeable = true;
  $contents = '';

  $target = $_SESSION['fm_directory'] . '/' . basename($_GET['entry']);

  if ( !is_writeable($target) ) {
    $writeable = false;

    $osC_MessageStack->add($osC_Template->getModule(), sprintf(ERROR_FILE_NOT_WRITEABLE, $target), 'warning');
  }

  $contents = file_get_contents($target);
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . osc_output_string_protected($_SESSION['fm_directory']); ?></div>
<div class="infoBoxContent">
  <form name="file_manager" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=save'); ?>" method="post">

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_FILE_NAME . '</b>'; ?></td>
      <td width="60%"><?php echo osc_output_string_protected($_GET['entry']) . osc_draw_hidden_field('filename', $_GET['entry']); ?></td>
    </tr>
    <tr>
      <td width="40%" valign="top"><?php echo '<b>' . TEXT_FILE_CONTENTS . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_textarea_field('contents', $contents, 80, 20, 'style="width: 100%;"' . (($writeable === true) ? '' : ' readonly="readonly"')); ?></td>
    </tr>
  </table>

  <p align="center">

<?php
  if ( $writeable === true ) {
    echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />';
  } else {
    echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />';
  }
?>

  </p>

  </form>
</div>
