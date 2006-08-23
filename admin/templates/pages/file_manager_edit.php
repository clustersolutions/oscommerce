<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><h1><?php echo HEADING_TITLE; ?></h1></td>
    <td class="smallText" align="right">
<?php
  echo '<form name="file_manager" action="' . osc_href_link_admin(FILENAME_FILE_MANAGER) . '" method="get">' .
       osc_draw_pull_down_menu('goto', $goto_array, substr($current_path, strlen(OSC_ADMIN_FILE_MANAGER_ROOT_PATH)+1), 'onchange="this.form.submit();"') .
       '</form>';
?>
    </td>
  </tr>
</table>

<?php
  $writeable = true;
  $contents = '';

  if (isset($_GET['entry']) && !empty($_GET['entry'])) {
    $target = $current_path . '/' . basename($_GET['entry']);

    if (file_exists($target)) {
      if (is_writeable($target) === false) {
        $writeable = false;

        echo '<p>' . sprintf(ERROR_FILE_NOT_WRITEABLE, $target) . '</p>';
      }

      $contents = file_get_contents($target);
    } else {
      $writeable = false;
    }
  } else {
    if (is_writeable($current_path) === false) {
      $writeable = false;

      echo '<p>' . sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, $current_path) . '</p>';
    }
  }
?>

<form name="file_manager_edit" action="<?php echo osc_href_link_admin(FILENAME_FILE_MANAGER, (isset($_GET['entry']) && !empty($_GET['entry']) ? 'entry=' . basename($_GET['entry']) . '&' : '') . 'action=save'); ?>" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="main"><?php echo TEXT_FILE_NAME; ?></td>
    <td class="main"><?php echo (isset($_GET['entry']) && !empty($_GET['entry']) ? $target : $current_path . '/' . osc_draw_input_field('filename')); ?></td>
  </tr>
  <tr>
    <td class="main" valign="top"><?php echo TEXT_FILE_CONTENTS; ?></td>
    <td class="main"><?php echo osc_draw_textarea_field('contents', $contents, 80, 20, 'style="width: 100%;"' . (($writeable) ? '' : ' readonly="readonly"')); ?></td>
  </tr>
</table>

<p align="right">
<?php
  if ($writeable === true) {
    echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton">&nbsp;';
  }

  echo '<input type="button" value="' . IMAGE_CANCEL . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_FILE_MANAGER, (isset($_GET['entry']) ? 'entry=' . $_GET['entry'] : '')) . '\';">';
?>
</p>

</form>
