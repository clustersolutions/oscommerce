<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $osC_DirectoryListing = new osC_DirectoryListing('../includes/services');
  $osC_DirectoryListing->setIncludeDirectories(false);
  $files = $osC_DirectoryListing->getFiles();
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo TABLE_HEADING_SERVICES; ?></th>
      <th width="150"><?php echo TABLE_HEADING_ACTION; ?></th>
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
    include('../includes/services/' . $file['name']);

    $class = substr($file['name'], 0, strrpos($file['name'], '.'));

    $module = 'osC_Services_' . $class;
    $module = new $module();
?>


    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" title="<?php echo $module->description; ?>">
      <td><?php echo $module->title; ?></td>
      <td align="right">

<?php
    if ( in_array($class, $osC_Template->_installed) && !osc_empty($module->keys()) ) {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $class . '&action=save'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;';
    } else {
      echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;';
    }

    if ( !in_array($class, $osC_Template->_installed) ) {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $class . '&action=install'), osc_icon('play.png', IMAGE_MODULE_INSTALL));
    } elseif ( $module->uninstallable ) {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $class . '&action=uninstall'), osc_icon('stop.png', IMAGE_MODULE_REMOVE));
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
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . TEXT_LEGEND . '</b> ' . osc_icon('configure.png', IMAGE_EDIT) . '&nbsp;' . IMAGE_EDIT . '&nbsp;&nbsp;' . osc_icon('play.png', IMAGE_MODULE_INSTALL) . '&nbsp;' . IMAGE_MODULE_INSTALL .  '&nbsp;&nbsp;' . osc_icon('stop.png', IMAGE_MODULE_REMOVE) . '&nbsp;' . IMAGE_MODULE_REMOVE; ?></td>
  </tr>
</table>
