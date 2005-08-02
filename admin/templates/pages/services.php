<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $directory_array = array();
  if ($dir = @dir('../includes/modules/services/')) {
    while ($file = $dir->read()) {
      if (!is_dir('../includes/modules/services/' . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          $directory_array[] = $file;
        }
      }
    }
    sort($directory_array);
    $dir->close();
  }
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_sDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_SERVICES; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  foreach ($directory_array as $service_module) {
    include('../includes/modules/services/' . $service_module);
    $class_code = substr($service_module, 0, strrpos($service_module, '.'));
    $class = 'osC_Services_' . $class_code;
    $module = new $class();

    $module_keys = $module->keys();

    if (!isset($sInfo) && (!isset($_GET['service']) || (isset($_GET['service']) && ($_GET['service'] == $class_code)))) {
      $module_info = array('code' => $class_code,
                           'title' => $module->title,
                           'description' => $module->description,
                           'status' => in_array($class_code, $installed),
                           'uninstallable' => $module->uninstallable,
                           'preceeds' => $module->preceeds,
                           'keys' => array());

      if (is_array($module_keys) && (sizeof($module_keys) > 0)) {
        foreach ($module_keys as $key) {
          $Qsm = $osC_Database->query('select configuration_title, configuration_key, configuration_value, configuration_description, use_function, set_function from :table_configuration where configuration_key = :configuration_key');
          $Qsm->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qsm->bindValue(':configuration_key', $key);
          $Qsm->execute();

          $module_info['keys'][$Qsm->value('configuration_key')] = array('title' => $Qsm->value('configuration_title'),
                                                                         'value' => $Qsm->value('configuration_value'),
                                                                         'description' => $Qsm->value('configuration_description'),
                                                                         'use_function' => $Qsm->value('use_function'),
                                                                         'set_function' => $Qsm->value('set_function'));
        }
      }

      $sInfo = new objectInfo($module_info);
    }

    if (isset($sInfo) && ($class_code == $sInfo->code) ) {
      echo '      <tr class="selected" title="' . $module->description . '">' . "\n";
    } else {
      echo '      <tr onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);" onClick="document.location.href=\'' . tep_href_link(FILENAME_SERVICES, 'service=' . $class_code) . '\';" title="' . $module->description . '">' . "\n";
    }
?>
        <td><?php echo (isset($module->title) ? $module->title : $class_code); ?></td>
        <td align="right">
<?php
    if (in_array($class_code, $installed) === false) {
      echo '<a href="' . tep_href_link(FILENAME_SERVICES, 'service=' . $class_code . '&action=install') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/play.png', IMAGE_MODULE_INSTALL, '16', '16') . '</a>&nbsp;';
    } elseif ($module->uninstallable) {
      if (isset($sInfo) && ($class_code == $sInfo->code) ) {
        echo '<a href="#" onClick="toggleInfoBox(\'sUninstall\');">' . tep_image('templates/' . $template . '/images/icons/16x16/stop.png', IMAGE_MODULE_REMOVE, '16', '16') . '</a>&nbsp;';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_SERVICES, 'service=' . $class_code . '&action=sDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/stop.png', IMAGE_MODULE_REMOVE, '16', '16') . '</a>&nbsp;';
      }
    } else {
      echo tep_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;';
    }

    if (is_array($module_keys) && (sizeof($module_keys) > 0)) {
      if (isset($sInfo) && ($class_code == $sInfo->code) ) {
        echo '<a href="#" onClick="toggleInfoBox(\'sEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_SERVICES, 'service=' . $class_code . '&action=sEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>';
      }
    } else {
      echo tep_image('images/pixel_trans.gif', '', '16', '16');
    }
?>
        </td>
      </tr>
<?php
  }
?>
    </tbody>
  </table>

  <p class="smallText"><?php echo TEXT_MODULE_DIRECTORY . ' ' . realpath(dirname(__FILE__) . '/../../../includes/modules/services/'); ?></p>
</div>

<?php
  if (isset($sInfo)) {
?>

<div id="infoBox_sUninstall" <?php if ($action != 'sDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/stop.png', IMAGE_MODULE_REMOVE, '16', '16') . ' ' . $sInfo->title; ?></div>
  <div class="infoBoxContent">
    <p><?php echo TEXT_UNINSTALL_INTRO; ?></p>
    <p><?php echo '<b>' . $sInfo->title . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_MODULE_REMOVE . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_SERVICES, 'service=' . $sInfo->code . '&action=remove') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'sDefault\');" class="operationButton">'; ?></p>
  </div>
</div>

<div id="infoBox_sEdit" <?php if ($action != 'sEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $sInfo->title; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('sEdit', FILENAME_SERVICES, 'service=' . $_GET['service'] . '&action=save'); ?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
    foreach ($sInfo->keys as $key => $value) {
      if (isset($value['set_function']) && !empty($value['set_function'])) {
        eval('$value_field = ' . $value['set_function'] . "'" . $value['value'] . "', '" . $key . "');");
      } else {
        $value_field = osc_draw_input_field('configuration[' . $key . ']', $value['value']);
      }
?>
      <tr>
        <td class="smallText" width="40%" valign="top"><?php echo '<b>' . $value['title'] . '</b><br>' . $value['description']; ?></td>
        <td class="smallText" width="60%" valign="top"><?php echo $value_field; ?></td>
      </tr>
<?php
    }
?>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_UPDATE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'sDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
