<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_DirectoryListing = new osC_DirectoryListing('../includes/modules/' . $_GET['set']);
  $osC_DirectoryListing->setIncludeDirectories(false);
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set']), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo TABLE_HEADING_MODULES; ?></th>
      <th width="150"><?php echo TABLE_HEADING_ACTION; ?></th>
    </tr>
  </thead>
  <tbody>

<?php
  foreach ( $osC_DirectoryListing->getFiles() as $file ) {
    include('../includes/modules/' . $_GET['set'] . '/' . $file['name']);

    $code = substr($file['name'], 0, strrpos($file['name'], '.'));
    $class = 'osC_' . ucfirst($_GET['set']) . '_' . $code;

   if ( class_exists($class) ) {
      if ( call_user_func(array($class, 'isInstalled'), $code, $_GET['set']) === false ) {
        $osC_Language->injectDefinitions('modules/' . $_GET['set'] . '/' . $code . '.xml');
      }

      $module = new $class();
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" <?php echo ( $module->isInstalled() && !$module->isActive() ? 'class="deactivatedRow"' : '') ?>>
      <td><?php echo $module->getTitle(); ?></td>
      <td align="right">

<?php
    if ( $module->isInstalled() && $module->isActive() ) {
      if ( $module->hasKeys() ) {
        echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&module=' . $code . '&action=save'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;';
      } else {
        echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;';
      }

      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&module=' . $code . '&action=uninstall'), osc_icon('stop.png', IMAGE_MODULE_REMOVE)) . '&nbsp;';
    } else {
      echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&module=' . $code . '&action=install'), osc_icon('play.png', IMAGE_MODULE_INSTALL)) . '&nbsp;';
    }

    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&module=' . $code . '&action=info'), osc_icon('info.png', IMAGE_INFO));
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

<p><?php echo TEXT_DIRECTORY . ' ' . $osC_DirectoryListing->getDirectory(); ?></p>
