<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/directory_listing.php');
  $osC_DirectoryListing = new osC_DirectoryListing('../includes/modules/' . $module_type);
  $osC_DirectoryListing->setIncludeDirectories(false);
  $files = $osC_DirectoryListing->getFiles();
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_mDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
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
    include('../includes/modules/' . $module_type . '/' . $file['name']);

    $class = substr($file['name'], 0, strrpos($file['name'], '.'));

    if (class_exists($module_class . $class)) {
      $osC_Language->injectDefinitions('modules/' . $module_type . '/' . $class . '.xml');

      $module = $module_class . $class;
      $module = new $module();

      if ($module->check() > 0) {
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
                             'installed' => ($module->check() ? true : false),
                             'status' => $module->getStatus());

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

      if (isset($mInfo) && ($class == $mInfo->code) ) {
        echo '      <tr class="selected">' . "\n";
      } else {
        echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . tep_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $class) . '\';">' . "\n";
      }
?>
        <td><?php echo $module->getTitle(); ?></td>
        <td><?php echo $module->getSortOrder(); ?></td>
        <td align="center"><?php echo tep_image('templates/' . $template . '/images/icons/' . (($module->check() > 0) ? ($module->getStatus() ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif') : 'checkbox.gif')); ?></td>
        <td align="right">
<?php
    if (isset($mInfo) && ($class == $mInfo->code)) {
      if ($mInfo->installed === true) {
        echo '<a href="#" onclick="toggleInfoBox(\'mUninstall\');">' . tep_image('templates/' . $template . '/images/icons/16x16/stop.png', IMAGE_MODULE_REMOVE, '16', '16') . '</a>&nbsp;' .
             '<a href="#" onclick="toggleInfoBox(\'mEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $class . '&action=install') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/play.png', IMAGE_MODULE_INSTALL, '16', '16') . '</a>&nbsp;' .
             tep_image('images/pixel_trans.gif', '', '16', '16');
      }
    } else {
      if ($module->check() > 0) {
        echo '<a href="' . tep_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $class . '&action=mUninstall') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/stop.png', IMAGE_MODULE_REMOVE, '16', '16') . '</a>&nbsp;' .
             '<a href="' . tep_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $class . '&action=mEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $class . '&action=install') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/play.png', IMAGE_MODULE_INSTALL, '16', '16') . '</a>&nbsp;' .
             tep_image('images/pixel_trans.gif', '', '16', '16');
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

  <p><?php echo TEXT_MODULE_DIRECTORY . ' ' . realpath(dirname(__FILE__) . '/../../../includes/modules/' . $module_type); ?></p>
</div>

<?php
  if (isset($mInfo)) {
?>

<div id="infoBox_mUninstall" <?php if ($action != 'mUninstall') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/stop.png', IMAGE_MODULE_REMOVE, '16', '16') . ' ' . $mInfo->title; ?></div>
  <div class="infoBoxContent">
    <p><?php echo INFO_MODULE_UNINSTALL_INTRO; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_MODULE_REMOVE . '" class="operationButton" onclick="document.location.href=\'' . tep_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $mInfo->code . '&action=remove') . '\';"> <input type="button" value="' . IMAGE_CANCEL . '" class="operationButton" onclick="toggleInfoBox(\'mDefault\');">'; ?></p>
  </div>
</div>

<div id="infoBox_mEdit" <?php if ($action != 'mEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $mInfo->title; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('mEdit', FILENAME_MODULES, 'set=' . $set . '&module=' . $mInfo->code . '&action=save'); ?>

<?php
    $keys = '';
    foreach ($mInfo->keys as $key => $value) {
      $keys .= '<b>' . $value['title'] . '</b><br />' . $value['description'] . '<br />';

      if ($value['set_function']) {
        eval('$keys .= ' . $value['set_function'] . "'" . $value['value'] . "', '" . $key . "');");
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
