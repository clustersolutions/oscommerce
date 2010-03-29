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

  $languages_array = array();

  foreach ( osC_Languages_Admin::getDirectoryListing() as $directory ) {
    $languages_array[] = array('id' => $directory,
                               'text' => $directory);
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_import_language'); ?></div>
<div class="infoBoxContent">
  <form name="lImport" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=import'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_import_language'); ?></p>

  <fieldset>
    <div><label for="language_import"><?php echo $osC_Language->get('field_language_selection'); ?></label><?php echo osc_draw_pull_down_menu('language_import', $languages_array); ?></div>
    <div><label for="import_type"><?php echo $osC_Language->get('field_import_type'); ?></label><?php echo osc_draw_radio_field('import_type', array(array('id' => 'add', 'text' => $osC_Language->get('only_add_new_records')), array('id' => 'update', 'text' => $osC_Language->get('only_update_existing_records')), array('id' => 'replace', 'text' => $osC_Language->get('replace_all'))), 'add', null, '<br />'); ?></div>
  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_import') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
