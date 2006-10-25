<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/directory_listing.php');
  $osC_DirectoryListing = new osC_DirectoryListing('includes/modules/' . $osC_Template->_module_type);
  $osC_DirectoryListing->setIncludeDirectories(false);
  $files = $osC_DirectoryListing->getFiles();
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $osC_Template->_module_type), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_mDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_MODULES; ?></th>
        <th><?php echo TABLE_HEADING_SORT_ORDER; ?></th>
        <th><?php echo TABLE_HEADING_STATUS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>

<?php
  $installed_modules = array();

  foreach ($files as $file) {
    include('includes/modules/' . $osC_Template->_module_type . '/' . $file['name']);

    $class = substr($file['name'], 0, strrpos($file['name'], '.'));

    if (class_exists($osC_Template->_module_class . $class)) {
      $osC_Language->injectDefinitions('modules/' . $osC_Template->_module_type . '/' . $class . '.xml');

      $module = $osC_Template->_module_class . $class;
      $module = new $module();

      if ($module->isInstalled()) {
        if ( ($module->getSortOrder() > 0) && !isset($installed_modules[$module->getSortOrder()])) {
          $installed_modules[$module->getSortOrder()] = $file['name'];
        } else {
          $installed_modules[] = $file['name'];
        }
      }

      if (!isset($mInfo) && (!isset($_GET['module']) || (isset($_GET['module']) && ($_GET['module'] == $class)))) {
        $module_info = array('code' => $module->getCode(),
                             'title' => $module->getTitle(),
                             'description' => $module->getDescription(),
                             'installed' => $module->isInstalled(),
                             'status' => $module->isEnabled());

        $module_keys = $module->getKeys();

        foreach ($module_keys as $key) {
          $Qkeys = $osC_Database->query('select configuration_title, configuration_value, configuration_description, use_function, set_function from :table_configuration where configuration_key = :configuration_key');
          $Qkeys->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qkeys->bindValue(':configuration_key', $key);
          $Qkeys->execute();

          $module_info['keys'][$key] = array('title' => $Qkeys->value('configuration_title'),
                                             'value' => $Qkeys->value('configuration_value'),
                                             'description' => $Qkeys->value('configuration_description'),
                                             'use_function' => $Qkeys->value('use_function'),
                                             'set_function' => $Qkeys->value('set_function'));
        }

        $mInfo = new objectInfo($module_info);
      }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td><?php echo $module->getTitle(); ?></td>
        <td><?php echo $module->getSortOrder(); ?></td>
        <td align="center"><?php echo osc_icon(($module->isInstalled() ? ($module->isEnabled() ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif') : 'checkbox.gif'), null, null); ?></td>
        <td align="right">

<?php
    if (isset($mInfo) && ($class == $mInfo->code)) {
      if ($mInfo->installed === true) {
        echo osc_link_object('#', osc_icon('stop.png', IMAGE_MODULE_REMOVE), 'onclick="toggleInfoBox(\'mUninstall\');"') . '&nbsp;' .
             osc_link_object('#', osc_icon('configure.png', IMAGE_EDIT), 'onclick="toggleInfoBox(\'mEdit\');"');
      } else {
        echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $osC_Template->_module_type . '&module=' . $class . '&action=install'), osc_icon('play.png', IMAGE_MODULE_INSTALL)) . '&nbsp;' .
             osc_image('images/pixel_trans.gif', '', '16', '16');
      }
    } else {
      if ($module->isInstalled()) {
        echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $osC_Template->_module_type . '&module=' . $class . '&action=mUninstall'), osc_icon('stop.png', IMAGE_MODULE_REMOVE)) . '&nbsp;' .
             osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $osC_Template->_module_type . '&module=' . $class . '&action=mEdit'), osc_icon('configure.png', IMAGE_EDIT));
      } else {
        echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $osC_Template->_module_type . '&module=' . $class . '&action=install'), osc_icon('play.png', IMAGE_MODULE_INSTALL)) . '&nbsp;' .
             osc_image('images/pixel_trans.gif', '', '16', '16');
      }
    }
?>

        </td>
      </tr>

<?php
    }
  }
?>

    </tbody>
  </table>
</div>

<?php
  if (isset($mInfo)) {
?>

<div id="infoBox_mUninstall" <?php if ($_GET['action'] != 'mUninstall') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('stop.png', IMAGE_MODULE_REMOVE) . ' ' . $mInfo->title; ?></div>
  <div class="infoBoxContent">
    <p><?php echo INFO_MODULE_UNINSTALL_INTRO; ?></p>

    <p align="center"><?php echo '<input type="button" value="' . IMAGE_MODULE_REMOVE . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $osC_Template->_module_type . '&module=' . $mInfo->code . '&action=remove') . '\';"> <input type="button" value="' . IMAGE_CANCEL . '" class="operationButton" onclick="toggleInfoBox(\'mDefault\');">'; ?></p>
  </div>
</div>

<div id="infoBox_mEdit" <?php if ($_GET['action'] != 'mEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $mInfo->title; ?></div>
  <div class="infoBoxContent">
    <form name="mEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $osC_Template->_module_type . '&module=' . $mInfo->code . '&action=save'); ?>" method="post">

<?php
    $keys = '';

    foreach ($mInfo->keys as $key => $value) {
      $keys .= '<b>' . $value['title'] . '</b><br />' . $value['description'] . '<br />';

      if ($value['set_function']) {
        $keys .= osc_call_user_func($value['set_function'], $value['value'], $key);
      } else {
        $keys .= osc_draw_input_field('configuration[' . $key . ']', $value['value']);
      }

      $keys .= '<br /><br />';
    }

    $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));
?>

    <p><?php echo $keys; ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'mDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
