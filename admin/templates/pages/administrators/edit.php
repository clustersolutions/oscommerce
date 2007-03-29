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

  $osC_DirectoryListing = new osC_DirectoryListing('includes/modules/access');
  $osC_DirectoryListing->setIncludeDirectories(false);

  $access_modules_array = array();

  foreach ($osC_DirectoryListing->getFiles() as $file) {
    $module = substr($file['name'], 0, strrpos($file['name'], '.'));

    if (!class_exists('osC_Access_' . ucfirst($module))) {
      $osC_Language->loadIniFile('modules/access/' . $file['name']);
      include($osC_DirectoryListing->getDirectory() . '/' . $file['name']);
    }

    $module = 'osC_Access_' . ucfirst($module);
    $module = new $module();

    $access_modules_array[osC_Access::getGroupTitle( $module->getGroup() )][] = array('id' => $module->getModule(),
                                                                                      'text' => $module->getTitle());
  }

  ksort($access_modules_array);

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Administrators_Admin::getData($_GET['aID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->get('user_name'); ?></div>
<div class="infoBoxContent">
  <form name="aEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&aID=' . $osC_ObjectInfo->get('id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_administrator'); ?></p>

  <p><?php echo '<b>' . $osC_Language->get('field_username') . '</b><br />' . osc_draw_input_field('user_name', $osC_ObjectInfo->get('user_name'), 'style="width: 100%;"'); ?></p>
  <p><?php echo '<b>' . $osC_Language->get('field_password') . '</b><br />' . osc_draw_password_field('user_password', 'style="width: 100%;"'); ?></p>

<?php
  echo '<ul style="list-style-type: none; padding-left: 0;">' .
       '  <li>' . osc_draw_checkbox_field('modules[]', '*', in_array('*', $osC_ObjectInfo->get('access_modules')), 'id="access_globaladmin"') . '&nbsp;<label for="access_globaladmin"><b>' . $osC_Language->get('global_access') . '</b></label></li>' .
       '</ul>' .
       '<ul style="list-style-type: none; padding-left: 0;">';

  foreach ( $access_modules_array as $group => $modules ) {
    echo '  <li><b>' . $group . '</b>' .
         '    <ul style="list-style-type: none; padding-left: 15px;">';

    foreach ($modules as $module) {
      echo '      <li>' . osc_draw_checkbox_field('modules[]', $module['id'], in_array($module['id'], $osC_ObjectInfo->get('access_modules')), 'id="access_' . $module['id'] . '"') . '&nbsp;<label for="access_' . $module['id'] . '" class="fieldLabel">' . $module['text'] . '</label></li>';
    }

    echo '    </ul>' .
         '  </li>';
  }

  echo '</ul>';
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
