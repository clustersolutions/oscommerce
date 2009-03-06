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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Languages_Admin::getDefinitionGroup($_GET['group']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . osc_output_string_protected($_GET['group']); ?></div>
<div class="infoBoxContent">
  <form name="gDelete" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . (int)$_GET[$osC_Template->getModule()] . '&group=' . $_GET['group'] . '&action=delete_group'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_delete_definition_group'); ?></p>

  <p><?php echo '<b>' . osc_output_string_protected($_GET['group']) . '</b>'; ?></p>

  <p>

<?php
  foreach ( $osC_ObjectInfo->get('entries') as $l ) {
    echo osC_Languages_Admin::get($l['languages_id'], 'name') . ': ' . (int)$l['total_entries'] . '<br />';
  }
?>

  </p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . (int)$_GET[$osC_Template->getModule()]) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
