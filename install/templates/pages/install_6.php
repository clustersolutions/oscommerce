<?php
/*
  $Id: install_6.php,v 1.8 2004/05/24 11:06:57 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $db_table_types = array(array('id' => 'mysql', 'text' => 'MySQL - MyISAM (Default)'),
                          array('id' => 'mysql_innodb', 'text' => 'MySQL - InnoDB (Transaction-Safe)'));
?>

<p class="pageTitle"><?php echo PAGE_TITLE_INSTALLATION; ?></p>

<p class="pageSubTitle"><?php echo PAGE_SUBTITLE_OSCOMMERCE_CONFIGURATION; ?></p>

<form name="install" action="install.php?step=7" method="post">

<table width="95%" border="0" cellpadding="2" class="formPage">
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_DATABASE_SERVER; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('DB_SERVER', (isset($_POST['DB_SERVER']) ? $_POST['DB_SERVER'] : '')); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('dbHost');"><br>
      <div id="dbHostSD"><?php echo CONFIG_DATABASE_SERVER_DESCRIPTION; ?></div>
      <div id="dbHost" class="longDescription"><?php echo CONFIG_DATABASE_SERVER_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_DATABASE_USERNAME; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('DB_SERVER_USERNAME', (isset($_POST['DB_SERVER_USERNAME']) ? $_POST['DB_SERVER_USERNAME'] : '')); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif"  onClick="toggleBox('dbUser');"><br>
      <div id="dbUserSD"><?php echo CONFIG_DATABASE_USERNAME_DESCRIPTION; ?></div>
      <div id="dbUser" class="longDescription"><?php echo CONFIG_DATABASE_USERNAME_RESTRICTED_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_DATABASE_PASSWORD; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_password_field('DB_SERVER_PASSWORD', (isset($_POST['DB_SERVER_PASSWORD']) ? $_POST['DB_SERVER_PASSWORD'] : '')); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('dbPass');"><br>
      <div id="dbPassSD"><?php echo CONFIG_DATABASE_PASSWORD_DESCRIPTION; ?></div>
      <div id="dbPass" class="longDescription"><?php echo CONFIG_DATABASE_PASSWORD_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_DATABASE_NAME; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('DB_DATABASE', (isset($_POST['DB_DATABASE']) ? $_POST['DB_DATABASE'] : '')); ?>
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
      <?php echo osc_draw_input_field('DB_TABLE_PREFIX', (isset($_POST['DB_TABLE_PREFIX']) ? $_POST['DB_TABLE_PREFIX'] : 'osc_')); ?>
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
      <?php echo osc_draw_checkbox_field('USE_PCONNECT', 'true', (isset($_POST['USE_PCONNECT']) && $_POST['USE_PCONNECT'] == 'true' ? true : false)); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('dbConn');"><br>
      <div id="dbConnSD"><?php echo CONFIG_DATABASE_PERSISTENT_CONNECTIONS_DESCRIPTION; ?></div>
      <div id="dbConn" class="longDescription"><?php echo CONFIG_DATABASE_PERSISTENT_CONNECTIONS_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_SESSION_STORAGE; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_radio_field('STORE_SESSIONS', 'files', (!isset($_POST['STORE_SESSIONS']) || (isset($_POST['STORE_SESSIONS']) && $_POST['STORE_SESSIONS'] == 'files') ? true : false)); ?>&nbsp;<?php echo CONFIG_SESSION_STORAGE_FILES; ?>&nbsp;&nbsp;<?php echo osc_draw_radio_field('STORE_SESSIONS', 'mysql', (isset($_POST['STORE_SESSIONS']) && $_POST['STORE_SESSIONS'] == 'mysql' ? true : false)); ?>&nbsp;<?php echo CONFIG_SESSION_STORAGE_DATABASE; ?>&nbsp;&nbsp;
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('dbSess');"><br>
      <div id="dbSessSD"><?php echo CONFIG_SESSION_STORAGE_DESCRIPTION; ?></div>
      <div id="dbSess" class="longDescription"><?php echo CONFIG_SESSION_STORAGE_DESCRIPTION_LONG; ?></div>
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
    if (($key != 'x') && ($key != 'y') && ($key != 'DB_SERVER') && ($key != 'DB_SERVER_USERNAME') && ($key != 'DB_SERVER_PASSWORD') && ($key != 'DB_DATABASE') && ($key != 'USE_PCONNECT') && ($key != 'STORE_SESSIONS')) {
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

</form>
