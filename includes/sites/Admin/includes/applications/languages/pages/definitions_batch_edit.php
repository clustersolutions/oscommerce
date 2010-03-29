<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . osc_output_string_protected($_GET['group']); ?></div>
<div class="infoBoxContent">
  <form name="lDefine" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . (int)$_GET[$osC_Template->getModule()] . '&group=' . $_GET['group'] . '&action=batch_save_definitions'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_language_definitions'); ?></p>

  <fieldset>

<?php
  foreach ( $_POST['batch'] as $id ) {
    $osC_ObjectInfo = new osC_ObjectInfo(osC_Languages_Admin::getDefinition($id));

    echo '<div><label for="def[' . $osC_ObjectInfo->getProtected('definition_key') . ']">' . $osC_ObjectInfo->getProtected('definition_key') . '</label>' . osc_draw_textarea_field('def[' . $osC_ObjectInfo->get('definition_key') . ']', $osC_ObjectInfo->get('definition_value')) . '</div>';
  }
?>

  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . (int)$_GET[$osC_Template->getModule()] . '&group=' . $_GET['group']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
