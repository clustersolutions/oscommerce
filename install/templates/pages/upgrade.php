<?php
/*
  $Id: upgrade.php,v 1.6 2004/05/24 11:06:57 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>

<p class="pageTitle"><?php echo PAGE_TITLE_UPGRADE; ?></p>

<?php echo TEXT_UPGRADE_DESCRIPTION; ?>

<form name="upgrade" action="upgrade.php?step=2" method="post">

<p><?php echo TEXT_ENTER_DATABASE_INFORMATION; ?>

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
</table>

<p>&nbsp;</p>

<table width="95%" border="0" cellspacing="2">
  <tr>
    <td align="right"><input type="image" src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/continue.gif" border="0" alt="<?php echo IMAGE_BUTTON_CONTINUE; ?>">&nbsp;&nbsp;<a href="index.php"><img src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/cancel.gif" border="0" alt="<?php echo IMAGE_BUTTON_CANCEL; ?>"></a></td>
  </tr>
</table>

</form>
