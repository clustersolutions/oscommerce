<?php
/*
  $Id: install.php,v 1.11 2004/05/24 11:06:57 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>

<p class="pageTitle"><?php echo PAGE_TITLE_INSTALLATION; ?></p>

<form name="install" action="install.php?step=2" method="post">

<p><?php echo TEXT_CUSTOMIZE_INSTALLATION; ?></p>

<table width="95%" border="0" cellpadding="2" class="formPage">
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_IMPORT_CATALOG_DATABASE; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_checkbox_field('install[]', 'database', true); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('dbImport');"><br>
      <div id="dbImportSD"><?php echo CONFIG_IMPORT_CATALOG_DATABASE_DESCRIPTION; ?></div>
      <div id="dbImport" class="longDescription"><?php echo CONFIG_IMPORT_CATALOG_DATABASE_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_AUTOMATIC_CONFIGURATION; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_checkbox_field('install[]', 'configure', true); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('autoConfig');"><br>
      <div id="autoConfigSD"><?php echo CONFIG_AUTOMATIC_CONFIGURATION_DESCRIPTION; ?></div>
      <div id="autoConfig" class="longDescription"><?php echo CONFIG_AUTOMATIC_CONFIGURATION_DESCRIPTION_LONG;?></div>
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
