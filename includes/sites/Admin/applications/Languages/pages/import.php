<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $languages_array = array();

  foreach ( OSCOM_Site_Admin_Application_Languages_Languages::getDirectoryListing() as $directory ) {
    $languages_array[] = array('id' => $directory,
                               'text' => $directory);
  }
?>

<h1><?php echo osc_link_object(OSCOM::getLink(), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('new.png') . ' ' . __('action_heading_import_language'); ?></h3>

  <form name="lImport" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'action=Import'); ?>" method="post">

  <p><?php echo __('introduction_import_language'); ?></p>

  <fieldset>
    <p><label for="language_import"><?php echo __('field_language_selection'); ?></label><?php echo osc_draw_pull_down_menu('language_import', $languages_array); ?></p>
    <p><label for="import_type"><?php echo __('field_import_type'); ?></label><br /><?php echo osc_draw_radio_field('import_type', array(array('id' => 'add', 'text' => __('only_add_new_records')), array('id' => 'update', 'text' => __('only_update_existing_records')), array('id' => 'replace', 'text' => __('replace_all'))), 'add', null, '<br />'); ?></p>
  </fieldset>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'triangle-1-se', 'title' => __('button_import'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => __('button_cancel'))); ?></p>

  </form>
</div>
