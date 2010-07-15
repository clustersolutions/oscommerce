<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;
  use osCommerce\OM\Core\OSCOM;

  $languages_array = array();

  foreach ( Languages::getDirectoryListing() as $directory ) {
    $languages_array[] = array('id' => $directory,
                               'text' => $directory);
  }
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('new.png') . ' ' . OSCOM::getDef('action_heading_import_language'); ?></h3>

  <form name="lImport" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'action=Import'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_import_language'); ?></p>

  <fieldset>
    <p><label for="language_import"><?php echo OSCOM::getDef('field_language_selection'); ?></label><?php echo osc_draw_pull_down_menu('language_import', $languages_array); ?></p>
    <p><label for="import_type"><?php echo OSCOM::getDef('field_import_type'); ?></label><br /><?php echo osc_draw_radio_field('import_type', array(array('id' => 'add', 'text' => OSCOM::getDef('only_add_new_records')), array('id' => 'update', 'text' => OSCOM::getDef('only_update_existing_records')), array('id' => 'replace', 'text' => OSCOM::getDef('replace_all'))), 'add', null, '<br />'); ?></p>
  </fieldset>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'triangle-1-se', 'title' => OSCOM::getDef('button_import'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
