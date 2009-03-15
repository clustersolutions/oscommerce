<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<div class="mainBlock">
  <ul style="list-style-type: none; padding: 5px; margin: 0px; display: inline; float: right;">
    <li style="font-weight: bold; display: inline;"><?php echo $osC_Language->get('title_language'); ?></li>
<?php
  foreach ($osC_Language->getAll() as $available_language) {
?>
    <li style="display: inline;"><?php echo '<a href="index.php?language=' . $available_language['code'] . '">' . $osC_Language->showImage($available_language['code']) . '</a>'; ?></li>
<?php      
  }
?>
  </ul>

  <h1><?php echo $osC_Language->get('page_title_welcome'); ?></h1>

  <p><?php echo $osC_Language->get('text_welcome'); ?></p>
</div>

<div class="contentBlock">
  <div class="infoPane">
    <h3><?php echo $osC_Language->get('box_server_title'); ?></h3>

    <div class="infoPaneContents">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td><b><?php echo $osC_Language->get('box_server_php_version'); ?></b></td>
          <td align="right"><?php echo phpversion(); ?></td>
          <td align="right" width="25"><img src="images/<?php echo ((version_compare(PHP_VERSION, '5.2') === 1) ? 'tick.gif' : 'cross.gif'); ?>" border="0" width="16" height="16"></td>
        </tr>
      </table>

      <br />

      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td><b><?php echo $osC_Language->get('box_server_php_settings'); ?></td>
          <td align="right"></td>
          <td align="right" width="25"></td>
        </tr>
        <tr>
          <td><?php echo $osC_Language->get('box_server_register_globals'); ?></td>
          <td align="right"><?php echo (((int)ini_get('register_globals') === 0) ? $osC_Language->get('box_server_off') : $osC_Language->get('box_server_on')); ?></td>
          <td align="right"><img src="images/<?php echo (((int)ini_get('register_globals') === 0) ? 'tick.gif' : 'cross.gif'); ?>" border="0" width="16" height="16"></td>
        </tr>
        <tr>
          <td><?php echo $osC_Language->get('box_server_magic_quotes'); ?></td>
          <td align="right"><?php echo (((int)ini_get('magic_quotes') === 0) ? $osC_Language->get('box_server_off') : $osC_Language->get('box_server_on')); ?></td>
          <td align="right"><img src="images/<?php echo (((int)ini_get('magic_quotes') === 0) ? 'tick.gif' : 'cross.gif'); ?>" border="0" width="16" height="16"></td>
        </tr>
        <tr>
          <td><?php echo $osC_Language->get('box_server_file_uploads'); ?></td>
          <td align="right"><?php echo (((int)ini_get('file_uploads') === 0) ? $osC_Language->get('box_server_off') : $osC_Language->get('box_server_on')); ?></td>
          <td align="right"><img src="images/<?php echo (((int)ini_get('file_uploads') === 1) ? 'tick.gif' : 'cross.gif'); ?>" border="0" width="16" height="16"></td>
        </tr>
        <tr>
          <td><?php echo $osC_Language->get('box_server_session_auto_start'); ?></td>
          <td align="right"><?php echo (((int)ini_get('session.auto_start') === 0) ? $osC_Language->get('box_server_off') : $osC_Language->get('box_server_on')); ?></td>
          <td align="right"><img src="images/<?php echo (((int)ini_get('session.auto_start') === 0) ? 'tick.gif' : 'cross.gif'); ?>" border="0" width="16" height="16"></td>
        </tr>
        <tr>
          <td><?php echo $osC_Language->get('box_server_session_use_trans_sid'); ?></td>
          <td align="right"><?php echo (((int)ini_get('session.use_trans_sid') === 0) ? $osC_Language->get('box_server_off') : $osC_Language->get('box_server_on')); ?></td>
          <td align="right"><img src="images/<?php echo (((int)ini_get('session.use_trans_sid') === 0) ? 'tick.gif' : 'cross.gif'); ?>" border="0" width="16" height="16"></td>
        </tr>
      </table>

      <br />

      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td><b><?php echo $osC_Language->get('box_server_php_extensions'); ?></b></td>
          <td align="right" width="25"></td>
        </tr>
        <tr>
          <td><?php echo $osC_Language->get('box_server_mysqli'); ?></td>
          <td align="right"><img src="images/<?php echo (extension_loaded('mysqli') ? 'tick.gif' : 'cross.gif'); ?>" border="0" width="16" height="16"></td>
        </tr>
        <tr>
          <td><?php echo $osC_Language->get('box_server_gd'); ?></td>
          <td align="right"><img src="images/<?php echo (extension_loaded('gd') ? 'tick.gif' : 'cross.gif'); ?>" border="0" width="16" height="16"></td>
        </tr>
        <tr>
          <td><?php echo $osC_Language->get('box_server_curl'); ?></td>
          <td align="right"><img src="images/<?php echo (extension_loaded('curl') ? 'tick.gif' : 'cross.gif'); ?>" border="0" width="16" height="16"></td>
        </tr>
        <tr>
          <td><?php echo $osC_Language->get('box_server_openssl'); ?></td>
          <td align="right"><img src="images/<?php echo (extension_loaded('openssl') ? 'tick.gif' : 'cross.gif'); ?>" border="0" width="16" height="16"></td>
        </tr>
      </table>
    </div>
  </div>

  <div class="contentPane">
    <h2><?php echo $osC_Language->get('page_heading_server_requirements'); ?></h2>

    <ul>
      <li>PHP v5.2+ (with MySQLi extension)</li>
      <li>MySQL v4.1.13+ or v5.0.7+</li>
    </ul>

    <h2><?php echo $osC_Language->get('page_heading_installation_type'); ?></h2>

<?php
  if (file_exists(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php') && !is_writeable(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php')) {
    @chmod(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php', 0777);
  }

  if (file_exists(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php') && !is_writeable(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php')) {
?>

    <div class="noticeBox">
      <?php echo sprintf($osC_Language->get('error_configuration_file_not_writeable'), osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php'); ?>

      <?php echo $osC_Language->get('error_configuration_file_alternate_method'); ?>
    </div>

    <br />

<?php
  }
?>

    <p><?php echo $osC_Language->get('text_installation_type'); ?></p>

    <table border="0" width="99%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%" align="center"><?php echo '<script type="text/javascript">document.write(\'<a href="install.php"><img src="templates/' . $template . '/languages/' . $osC_Language->getCode() . '/images/install.gif" border="0" alt="' . addslashes($osC_Language->get('image_button_install')) . '" /></a>\');</script>'; ?></td>

<!--
        <td width="50%" align="center"><?php echo '<a href="upgrade.php"><img src="templates/' . $template . '/languages/' . $osC_Language->getCode() . '/images/upgrade.gif" border="0" alt="' . $osC_Language->get('image_button_upgrade') . '" /></a>'; ?></td>
//-->

      </tr>
    </table>

    <noscript>
      <div class="noticeBox">
        <?php echo $osC_Language->get('error_javascript_disabled'); ?>
      </div>
    </noscript>

  </div>
</div>
