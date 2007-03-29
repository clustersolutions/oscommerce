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

  $languages_array = array();

  $osC_DirectoryListing = new osC_DirectoryListing('../includes/languages');
  $osC_DirectoryListing->setIncludeDirectories(false);
  $osC_DirectoryListing->setCheckExtension('xml');

  foreach ($osC_DirectoryListing->getFiles() as $file) {
    $languages_array[] = array('id' => substr($file['name'], 0, strrpos($file['name'], '.')), 'text' => substr($file['name'], 0, strrpos($file['name'], '.')));
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_import_language'); ?></div>
<div class="infoBoxContent">
  <form name="lImport" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=import'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_import_language'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_language_selection') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_pull_down_menu('language_import', $languages_array, null, 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_import_type') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_radio_field('import_type', array(array('id' => 'add', 'text' => $osC_Language->get('only_add_new_records')), array('id' => 'update', 'text' => $osC_Language->get('only_update_existing_records')), array('id' => 'replace', 'text' => $osC_Language->get('replace_all'))), 'add', null, '<br />'); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_import') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
