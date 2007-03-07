<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_DirectoryListing = new osC_DirectoryListing('includes/templates');
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
      <th><?php echo TABLE_HEADING_TEMPLATES; ?></th>
      <th width="150"><?php echo TABLE_HEADING_ACTION; ?></th>
    </tr>
  </thead>
  <tbody>

<?php
  foreach ( $files as $file ) {
    include('includes/templates/' . $file['name']);

    $code = substr($file['name'], 0, strrpos($file['name'], '.'));
    $class = 'osC_Template_' . $code;

    if ( class_exists($class) ) {
      $module = new $class();

      $module_title = $module->getTitle();

      if ( $module->getCode() == DEFAULT_TEMPLATE ) {
        $module_title .= ' (' . TEXT_DEFAULT . ')';
      }
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" <?php echo ( $module->isInstalled() && !$module->isActive() ? 'class="deactivatedRow"' : '') ?>>
      <td><?php echo $module_title; ?></td>
      <td align="right">

<?php
      if ( $module->isInstalled() && $module->isActive() ) {
        if ( $module->hasKeys() || ( $module->getCode() != DEFAULT_TEMPLATE ) ) {
          echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&template=' . $code . '&action=save'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;';
        } else {
          echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;';
        }

        echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&template=' . $code . '&action=uninstall'), osc_icon('stop.png', IMAGE_MODULE_REMOVE)) . '&nbsp;';
      } else {
        echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;' .
             osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&template=' . $code . '&action=install'), osc_icon('play.png', IMAGE_MODULE_INSTALL)) . '&nbsp;';
      }

      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&template=' . $code . '&action=info'), osc_icon('info.png', IMAGE_INFO));
?>

      </td>
    </tr>

<?php
    }
  }
?>

  </tbody>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . TEXT_LEGEND . '</b> ' . osc_icon('configure.png', IMAGE_EDIT) . '&nbsp;' . IMAGE_EDIT . '&nbsp;&nbsp;' . osc_icon('play.png', IMAGE_MODULE_INSTALL) . '&nbsp;' . IMAGE_MODULE_INSTALL .  '&nbsp;&nbsp;' . osc_icon('stop.png', IMAGE_MODULE_REMOVE) . '&nbsp;' . IMAGE_MODULE_REMOVE . '&nbsp;&nbsp;' . osc_icon('info.png', IMAGE_INFO) . '&nbsp;' . IMAGE_INFO; ?></td>
  </tr>
</table>
