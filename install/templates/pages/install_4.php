<?php
/*
  $Id: install_4.php,v 1.15 2004/05/24 11:06:57 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>

<p class="pageTitle"><?php echo PAGE_TITLE_INSTALLATION; ?></p>

<p class="pageSubTitle"><?php echo PAGE_SUBTITLE_OSCOMMERCE_CONFIGURATION; ?></p>

<?php
  if (isset($_POST['HTTP_WWW_ADDRESS'])) {
    if (file_exists($_POST['HTTP_WORK_DIRECTORY'])) {
      if (is_writeable($_POST['HTTP_WORK_DIRECTORY'])) {
        if ($fp = @fopen($_POST['HTTP_WORK_DIRECTORY'] . '/.htaccess', 'w')) {
          flock($fp, 2); // LOCK_EX
          fputs($fp, "<Files *>\nOrder Deny,Allow\nDeny from all\n</Files>");
          flock($fp, 3); // LOCK_UN
          fclose($fp);

          $error = false;
        } else {
          $error = ERROR_WORK_DIRECTORY_NOT_WRITEABLE;
        }
      } else {
        $error = ERROR_WORK_DIRECTORY_NOT_WRITEABLE;
      }
    } else {
      $error = ERROR_WORK_DIRECTORY_NON_EXISTANT;
    }

    if ($error !== false) {
?>

<form name="install" action="install.php?step=4" method="post">

<table width="95%" border="0" cellpadding="2" class="formPage">
  <tr>
    <td><?php echo sprintf($error, $_POST['HTTP_WORK_DIRECTORY']); ?></td>
  </tr>
</table>

<?php
      foreach($_POST as $key => $value) {
        if (($key != 'x') && ($key != 'y')) {
          if (is_array($value)) {
            for ($i=0, $n=sizeof($value); $i<$n; $i++) {
              echo osc_draw_hidden_field($key . '[]', $value[$i]);
            }
          } else {
            echo osc_draw_hidden_field($key, $value);
          }
        }
      }
?>

<p>&nbsp;</p>

<table width="95%" border="0" cellspacing="2">
  <tr>
    <td align="right"><input type="image" src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/retry.gif" border="0" alt="<?php echo IMAGE_BUTTON_RETRY; ?>">&nbsp;&nbsp;<a href="index.php"><img src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/cancel.gif" border="0" alt="<?php echo IMAGE_BUTTON_CANCEL; ?>"></a></td>
  </tr>
</table>

</form>

<?php
    } else {
?>

<form name="install" action="install.php?step=5" method="post">

<table width="95%" border="0" cellpadding="2" class="formPage">
  <tr>
    <td><?php echo TEXT_SUCCESSFUL_CONFIGURATION; ?></td>
  </tr>
</table>

<?php
      foreach ($_POST as $key => $value) {
        if (($key != 'x') && ($key != 'y')) {
          if (is_array($value)) {
            for ($i=0, $n=sizeof($value); $i<$n; $i++) {
              echo osc_draw_hidden_field($key . '[]', $value[$i]);
            }
          } else {
            echo osc_draw_hidden_field($key, $value);
          }
        }
      }
?>

<p>&nbsp;</p>

<table width="95%" border="0" cellspacing="2">
  <tr>
    <td align="right"><input type="image" src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/continue.gif" border="0" alt="<?php echo IMAGE_BUTTON_CONTINUE; ?>">&nbsp;&nbsp;<a href="index.php"><img src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/cancel.gif" border="0" alt="<?php echo IMAGE_BUTTON_CANCEL; ?>"></a></td>
  </tr>
</table>

</form>

<?php
    }
  } else {
    $cookie_path = substr(dirname(getenv('SCRIPT_NAME')), 0, -7);

    $www_location = 'http://' . getenv('HTTP_HOST') . getenv('SCRIPT_NAME');
    $www_location = substr($www_location, 0, strpos($www_location, 'install'));

    $script_filename = getenv('PATH_TRANSLATED');
    if (empty($script_filename)) {
      $script_filename = getenv('SCRIPT_FILENAME');
    }

    $script_filename = str_replace('\\', '/', $script_filename);
    $script_filename = str_replace('//', '/', $script_filename);

    $dir_fs_www_root_array = explode('/', dirname($script_filename));
    $dir_fs_www_root = array();
    for ($i=0, $n=sizeof($dir_fs_www_root_array)-1; $i<$n; $i++) {
      $dir_fs_www_root[] = $dir_fs_www_root_array[$i];
    }
    $dir_fs_www_root = implode('/', $dir_fs_www_root) . '/';
?>

<form name="install" action="install.php?step=4" method="post">

<p><?php echo TEXT_ENTER_WEBSERVER_INFORMATION; ?></p>

<table width="95%" border="0" cellpadding="2" class="formPage">
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_WWW_ADDRESS; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('HTTP_WWW_ADDRESS', $www_location); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('httpWWW');"><br>
      <div id="httpWWWSD"><?php echo CONFIG_WWW_ADDRESS_DESCRIPTION; ?></div>
      <div id="httpWWW" class="longDescription"><?php echo CONFIG_WWW_ADDRESS_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_WWW_ROOT_DIRECTORY; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('DIR_FS_DOCUMENT_ROOT', $dir_fs_www_root); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif"  onClick="toggleBox('httpRoot');"><br>
      <div id="httpRootSD"><?php echo CONFIG_WWW_ROOT_DIRECTORY_DESCRIPTION; ?></div>
      <div id="httpRoot" class="longDescription"><?php echo CONFIG_WWW_ROOT_DIRECTORY_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_WWW_HTTP_COOKIE_DOMAIN; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('HTTP_COOKIE_DOMAIN', getenv('HTTP_HOST')); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('httpCookieD');"><br>
      <div id="httpCookieDSD"><?php echo CONFIG_WWW_HTTP_COOKIE_DOMAIN_DESCRIPTION; ?></div>
      <div id="httpCookieD" class="longDescription"><?php echo CONFIG_WWW_HTTP_COOKIE_DOMAIN_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_WWW_HTTP_COOKIE_PATH; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('HTTP_COOKIE_PATH', $cookie_path); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('httpCookieP');"><br>
      <div id="httpCookiePSD"><?php echo CONFIG_WWW_HTTP_COOKIE_PATH_DESCRIPTION; ?></div>
      <div id="httpCookieP" class="longDescription"><?php echo CONFIG_WWW_HTTP_COOKIE_PATH_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_ENABLE_SSL; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_checkbox_field('ENABLE_SSL', 'true'); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('httpSSL');"><br>
      <div id="httpSSLSD"><?php echo CONFIG_ENABLE_SSL_DESCRIPTION; ?></div>
      <div id="httpSSL" class="longDescription"><?php echo CONFIG_ENABLE_SSL_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_WWW_WORK_DIRECTORY; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('HTTP_WORK_DIRECTORY', $dir_fs_www_root . 'oscommerce_data'); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('httpWork');"><br>
      <div id="httpWorkSD"><?php echo CONFIG_WWW_WORK_DIRECTORY_DESCRIPTION; ?></div>
      <div id="httpWork" class="longDescription"><?php echo CONFIG_WWW_WORK_DIRECTORY_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
</table>

<p>&nbsp;</p>

<table width="95%" border="0" cellspacing="2">
  <tr>
    <td align="right"><input type="image" src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/continue.gif" border="0" alt="<?php echo IMAGE_BUTTON_CONTINUE; ?>">&nbsp;&nbsp;<a href="index.php"><img src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/cancel.gif" border="0" alt="<?php echo IMAGE_BUTTON_CANCEL; ?>"></a></td>
  </tr>
</table>

<?php
    foreach ($_POST as $key => $value) {
      if (($key != 'x') && ($key != 'y')) {
        if (is_array($value)) {
          for ($i=0, $n=sizeof($value); $i<$n; $i++) {
            echo osc_draw_hidden_field($key . '[]', $value[$i]);
          }
        } else {
          echo osc_draw_hidden_field($key, $value);
        }
      }
    }

    echo osc_draw_hidden_field('install[]', 'configure');
?>

</form>

<?php
  }
?>
