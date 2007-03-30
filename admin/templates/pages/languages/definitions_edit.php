<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $_GET['group']; ?></div>
<div class="infoBoxContent">
  <form name="lDefine" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&group=' . $_GET['group'] . '&action=definitions'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_language_definitions'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  $Qdefs = $osC_Database->query('select definition_key, definition_value from :table_languages_definitions where languages_id = :languages_id and content_group = :content_group order by definition_key');
  $Qdefs->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
  $Qdefs->bindInt(':languages_id', $_GET[$osC_Template->getModule()]);
  $Qdefs->bindValue(':content_group', $_GET['group']);
  $Qdefs->execute();

  while ($Qdefs->next()) {
?>

    <tr>
      <td width="40%"><?php echo '<b>' . $Qdefs->value('definition_key') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_textarea_field('def[' . $Qdefs->value('definition_key') . ']', $Qdefs->value('definition_value'), 60, 4, 'style="width: 100%"'); ?></td>
    </tr>

<?php
  }
?>

  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&group=' . $_GET['group']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
