<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_delete_entry'); ?></div>
<div class="infoBoxContent">
  <form name="fmDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&entry=' . $_GET['entry'] . '&action=delete'); ?>" method="post">

<?php
  if ( is_writeable($_SESSION['fm_directory'] . '/' . $_GET['entry']) ) {
?>

  <p><?php echo $osC_Language->get('introduction_delete_entry'); ?></p>

  <p><?php echo '<b>' . osc_output_string_protected($_SESSION['fm_directory']) . '/' . osc_output_string_protected($_GET['entry']) . '</b>'; ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

<?php
  } else {
?>

  <p><?php echo sprintf($osC_Language->get('delete_error_not_writable'), $_SESSION['fm_directory'] . '/' . $_GET['entry']); ?></p>

  <p align="center"><?php echo '<input type="button" value="' . $osC_Language->get('button_retry') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&entry=' . $_GET['entry'] . '&action=delete') . '\';" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

<?php
  }
?>

  </form>
</div>
