<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<?php
  $file = realpath('../includes/languages/' . $lng . '/' . $_GET['file']);

  if (substr($file, 0, strlen(realpath('../includes/languages'))) != realpath('../includes/languages')) {
    $file = false;
  }

  if (file_exists($file)) {
    $contents = file_get_contents($file);

    $file_writeable = true;

    if (is_writeable($file) !== true) {
      $file_writeable = false;

      $osC_MessageStack->add('define_language', sprintf(ERROR_FILE_NOT_WRITEABLE, $file), 'error');
      echo $osC_MessageStack->output('define_language');
    }
?>

<?php echo tep_draw_form('language', FILENAME_DEFINE_LANGUAGE, 'lng=' . $lng . '&file=' . $_GET['file'] . '&action=save'); ?>

<p><b><?php echo realpath('../includes/languages/' . $lng . '/' . $_GET['file']); ?></b></p>

<p><?php echo osc_draw_textarea_field('contents', $contents, '80', '20', 'off', 'style="width: 100%;"' . (($file_writeable) ? '' : ' readonly')); ?></p>

<p align="right"><?php if ($file_writeable === true) { echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton">&nbsp;'; } echo '<input type="button" value="' . IMAGE_CANCEL . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_DEFINE_LANGUAGE, 'lng=' . $lng) . '\';" class="operationButton">'; ?></p>

<?php
  } else {
?>

<p><?php echo TEXT_FILE_DOES_NOT_EXIST; ?></p>

<p><?php echo '<input type="button" value="' . IMAGE_BACK . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_DEFINE_LANGUAGE, 'lng=' . $lng) . '\';" class="operationButton">'; ?></p>

<?php
  }
?>
