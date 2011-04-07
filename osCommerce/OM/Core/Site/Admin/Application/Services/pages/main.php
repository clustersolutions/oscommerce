<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_DirectoryListing = new osC_DirectoryListing('includes/modules/services');
  $osC_DirectoryListing->setIncludeDirectories(false);
  $files = $osC_DirectoryListing->getFiles();
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_service_modules'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="2">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>

<?php
  foreach ($files as $file) {
    include('includes/modules/services/' . $file['name']);

    $class = substr($file['name'], 0, strrpos($file['name'], '.'));

    $module = 'osC_Services_' . $class . '_Admin';
    $module = new $module();
?>


    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" title="<?php echo $module->description; ?>">
      <td><?php echo $module->title; ?></td>
      <td align="right">

<?php
    if ( in_array($class, $osC_Template->_installed) && !osc_empty($module->keys()) ) {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $class . '&action=save'), osc_icon('edit.png')) . '&nbsp;';
    } else {
      echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;';
    }

    if ( !in_array($class, $osC_Template->_installed) ) {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $class . '&action=install'), osc_icon('install.png'));
    } elseif ( $module->uninstallable ) {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $class . '&action=uninstall'), osc_icon('uninstall.png'));
    } else {
      echo osc_image('images/pixel_trans.gif', '', '16', '16');
    }
?>

      </td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('install.png') . '&nbsp;' . $osC_Language->get('icon_install') .  '&nbsp;&nbsp;' . osc_icon('uninstall.png') . '&nbsp;' . $osC_Language->get('button_uninstall'); ?></td>
  </tr>
</table>
