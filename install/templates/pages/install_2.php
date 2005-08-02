<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $db_table_types = array(array('id' => 'mysql', 'text' => 'MySQL - MyISAM (Default)'),
                          array('id' => 'mysql_innodb', 'text' => 'MySQL - InnoDB (Transaction-Safe)'));
?>

<p class="pageTitle"><?php echo PAGE_TITLE_INSTALLATION; ?></p>

<p class="pageSubTitle"><?php echo PAGE_SUBTITLE_DATABASE_IMPORT; ?></p>

<?php
  if (isset($_POST['DB_TEST_CONNECTION']) && ($_POST['DB_TEST_CONNECTION'] == 'true')) {
    $db = array('DB_SERVER' => trim($_POST['DB_SERVER']),
                'DB_SERVER_USERNAME' => trim($_POST['DB_SERVER_USERNAME']),
                'DB_SERVER_PASSWORD' => trim($_POST['DB_SERVER_PASSWORD']),
                'DB_DATABASE' => trim($_POST['DB_DATABASE']),
                'DB_TABLE_PREFIX' => trim($_POST['DB_TABLE_PREFIX']),
                'DB_DATABASE_CLASS' => trim($_POST['DB_DATABASE_CLASS']));

    $osC_Database = osC_Database::connect($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD'], $db['DB_DATABASE_CLASS']);

    if ($osC_Database->isError() === false) {
      $osC_Database->hasCreatePermission($db['DB_DATABASE']);
    }

    if ($osC_Database->isError()) {
?>
<form name="install" action="install.php?step=2" method="post">

<table width="95%" border="0" cellpadding="2" class="formPage">
  <tr>
    <td><?php echo sprintf(ERROR_UNSUCCESSFUL_DATABASE_CONNECTION, $osC_Database->getError()); ?></td>
  </tr>
</table>

<?php
      foreach($_POST as $key => $value) {
        if (($key != 'x') && ($key != 'y') && ($key != 'DB_TEST_CONNECTION')) {
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
    <td align="right"><input type="image" src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/back.gif" border="0" alt="<?php echo IMAGE_BUTTON_BACK; ?>">&nbsp;&nbsp;<a href="index.php"><img src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/cancel.gif" border="0" alt="<?php echo IMAGE_BUTTON_CANCEL; ?>"></a></td>
  </tr>
</table>

</form>

<?php
    } else {
      if ($_POST['DB_DATABASE_CLASS'] == 'mysql_innodb') {
        $db_has_innodb = false;

        $Qinno = $osC_Database->query('show variables like "have_innodb"');
        if (($Qinno->numberOfRows() === 1) && (strtolower($Qinno->value('Value')) == 'yes')) {
          $db_has_innodb = true;
        }
      }

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

<form name="install" action="install.php?step=3" method="post">

<table width="95%" border="0" cellpadding="2" class="formPage">
  <tr>
    <td>
<?php
      echo TEXT_SUCCESSFUL_DATABASE_CONNECTION;

      echo sprintf(TEXT_IMPORT_SQL, $dir_fs_www_root . 'install/oscommerce.sql');

      if (isset($_POST['DB_INSERT_SAMPLE_DATA']) && ($_POST['DB_INSERT_SAMPLE_DATA'] == 'true')) {
        echo sprintf(TEXT_IMPORT_DATA_SAMPLE_SQL, $dir_fs_www_root . 'install/oscommerce_sample_data.sql');
      }
?>
    </td>
  </tr>
</table>

<?php
      if ( ($_POST['DB_DATABASE_CLASS'] == 'mysql_innodb') && ($db_has_innodb === false) ) {
?>

<table width="95%" border="0" cellpadding="2">
  <tr>
    <td class="boxme"><?php echo sprintf(ERROR_UNSUCCESSFUL_DATABASE_TYPE, 'mysql_innodb', 'mysql'); ?></td>
  </tr>
</table>

<?php
      }

      foreach ($_POST as $key => $value) {
        if (($key != 'x') && ($key != 'y') && ($key != 'DB_TEST_CONNECTION')) {
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
?>

<form name="install" action="install.php?step=2" method="post">

<p><?php echo TEXT_ENTER_DATABASE_INFORMATION; ?></p>

<table width="95%" border="0" cellpadding="2" class="formPage">
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_DATABASE_SERVER; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('DB_SERVER'); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('dbHost');"><br>
      <div id="dbHostSD"><?php echo CONFIG_DATABASE_SERVER_DESCRIPTION; ?></div>
      <div id="dbHost" class="longDescription"><?php echo CONFIG_DATABASE_SERVER_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_DATABASE_USERNAME; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('DB_SERVER_USERNAME'); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif"  onClick="toggleBox('dbUser');"><br>
      <div id="dbUserSD"><?php echo CONFIG_DATABASE_USERNAME_DESCRIPTION; ?></div>
      <div id="dbUser" class="longDescription"><?php echo CONFIG_DATABASE_USERNAME_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_DATABASE_PASSWORD; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_password_field('DB_SERVER_PASSWORD'); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('dbPass');"><br>
      <div id="dbPassSD"><?php echo CONFIG_DATABASE_PASSWORD_DESCRIPTION; ?></div>
      <div id="dbPass" class="longDescription"><?php echo CONFIG_DATABASE_PASSWORD_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_DATABASE_NAME; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('DB_DATABASE'); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('dbName');"><br>
      <div id="dbNameSD"><?php echo CONFIG_DATABASE_NAME_DESCRIPTION; ?></div>
      <div id="dbName" class="longDescription"><?php echo CONFIG_DATABASE_NAME_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_DATABASE_CLASS; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_pull_down_menu('DB_DATABASE_CLASS', $db_table_types); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('dbClass');"><br>
      <div id="dbClassSD"><?php echo CONFIG_DATABASE_CLASS_DESCRIPTION; ?></div>
      <div id="dbClass" class="longDescription"><?php echo CONFIG_DATABASE_CLASS_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_DATABASE_TABLE_PREFIX; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('DB_TABLE_PREFIX', 'osc_'); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('dbNamePrefix');"><br>
      <div id="dbNamePrefixSD"><?php echo CONFIG_DATABASE_TABLE_PREFIX_DESCRIPTION; ?></div>
      <div id="dbNamePrefix" class="longDescription"><?php echo CONFIG_DATABASE_TABLE_PREFIX_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_DATABASE_PERSISTENT_CONNECTIONS; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_checkbox_field('USE_PCONNECT', 'true'); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('dbConn');"><br>
      <div id="dbConnSD"><?php echo CONFIG_DATABASE_PERSISTENT_CONNECTIONS_DESCRIPTION; ?></div>
      <div id="dbConn" class="longDescription"><?php echo CONFIG_DATABASE_PERSISTENT_CONNECTIONS_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_SESSION_STORAGE; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_radio_field('STORE_SESSIONS', 'files', true); ?>&nbsp;<?php echo CONFIG_SESSION_STORAGE_FILES; ?>&nbsp;&nbsp;<?php echo osc_draw_radio_field('STORE_SESSIONS', 'mysql'); ?>&nbsp;<?php echo CONFIG_SESSION_STORAGE_DATABASE; ?>&nbsp;&nbsp;
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('dbSess');"><br>
      <div id="dbSessSD"><?php echo CONFIG_SESSION_STORAGE_DESCRIPTION; ?></div>
      <div id="dbSess" class="longDescription"><?php echo CONFIG_SESSION_STORAGE_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_IMPORT_SAMPLE_DATA; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_checkbox_field('DB_INSERT_SAMPLE_DATA', 'true', (!isset($_POST['DB_SERVER']) || (isset($_POST['DB_INSERT_SAMPLE_DATA']) && ($_POST['DB_INSERT_SAMPLE_DATA'] == 'true')) ? true : false)); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('dbSample');"><br>
      <div id="dbSampleSD"><?php echo CONFIG_IMPORT_SAMPLE_DATA_DESCRIPTION; ?></div>
      <div id="dbSample" class="longDescription"><?php echo CONFIG_IMPORT_SAMPLE_DATA_DESCRIPTION_LONG; ?></div>
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
    if (($key != 'x') && ($key != 'y') && ($key != 'DB_SERVER') && ($key != 'DB_SERVER_USERNAME') && ($key != 'DB_SERVER_PASSWORD') && ($key != 'DB_DATABASE') && ($key != 'DB_DATABASE_CLASS') && ($key != 'DB_TABLE_PREFIX') && ($key != 'USE_PCONNECT') && ($key != 'STORE_SESSIONS') && ($key != 'DB_INSERT_SAMPLE_DATA') && ($key != 'DB_TEST_CONNECTION')) {
      if (is_array($value)) {
        for ($i=0, $n=sizeof($value); $i<$n; $i++) {
          echo osc_draw_hidden_field($key . '[]', $value[$i]);
        }
      } else {
        echo osc_draw_hidden_field($key, $value);
      }
    }
  }

  echo osc_draw_hidden_field('DB_TEST_CONNECTION', 'true');
?>

</form>

<?php
  }
?>
