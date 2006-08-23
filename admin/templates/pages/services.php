<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $directory_array = array();
  if ($dir = @dir('../includes/services/')) {
    while ($file = $dir->read()) {
      if (!is_dir('../includes/services/' . $file)) {
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
    include('../includes/services/' . $service_module);
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
                           'precedes' => $module->precedes,
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
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_SERVICES, 'service=' . $class_code) . '\';" title="' . $module->description . '">' . "\n";
    }
?>
        <td><?php echo (isset($module->title) ? $module->title : $class_code); ?></td>
        <td align="right">
<?php
    if (in_array($class_code, $installed) === false) {
      echo osc_link_object(osc_href_link_admin(FILENAME_SERVICES, 'service=' . $class_code . '&action=install'), osc_icon('play.png', IMAGE_MODULE_INSTALL)) . '&nbsp;';
    } elseif ($module->uninstallable) {
      if (isset($sInfo) && ($class_code == $sInfo->code) ) {
        echo '<a href="#" onclick="toggleInfoBox(\'sUninstall\');">' . osc_icon('stop.png', IMAGE_MODULE_REMOVE) . '</a>&nbsp;';
      } else {
        echo osc_link_object(osc_href_link_admin(FILENAME_SERVICES, 'service=' . $class_code . '&action=sDelete'), osc_icon('stop.png', IMAGE_MODULE_REMOVE)) . '&nbsp;';
      }
    } else {
      echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;';
    }

    if (is_array($module_keys) && (sizeof($module_keys) > 0)) {
      if (isset($sInfo) && ($class_code == $sInfo->code) ) {
        echo '<a href="#" onclick="toggleInfoBox(\'sEdit\');">' . osc_icon('configure.png', IMAGE_EDIT) . '</a>';
      } else {
        echo osc_link_object(osc_href_link_admin(FILENAME_SERVICES, 'service=' . $class_code . '&action=sEdit'), osc_icon('configure.png', IMAGE_EDIT));
      }
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

  <p class="smallText"><?php echo TEXT_MODULE_DIRECTORY . ' ' . realpath(dirname(__FILE__) . '/../../../includes/services/'); ?></p>
</div>

<?php
  if (isset($sInfo)) {
?>

<div id="infoBox_sUninstall" <?php if ($action != 'sDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('stop.png', IMAGE_MODULE_REMOVE) . ' ' . $sInfo->title; ?></div>
  <div class="infoBoxContent">
    <p><?php echo TEXT_UNINSTALL_INTRO; ?></p>
    <p><?php echo '<b>' . $sInfo->title . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_MODULE_REMOVE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_SERVICES, 'service=' . $sInfo->code . '&action=remove') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'sDefault\');" class="operationButton">'; ?></p>
  </div>
</div>

<div id="infoBox_sEdit" <?php if ($action != 'sEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $sInfo->title; ?></div>
  <div class="infoBoxContent">
    <form name="sEdit" action="<?php echo osc_href_link_admin(FILENAME_SERVICES, 'service=' . $_GET['service'] . '&action=save'); ?>" method="post">

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
    foreach ($sInfo->keys as $key => $value) {
      if (isset($value['set_function']) && !empty($value['set_function'])) {
        $value_field = osc_call_user_func($value['set_function'], $value['value'], $key);
      } else {
        $value_field = osc_draw_input_field('configuration[' . $key . ']', $value['value']);
      }
?>
      <tr>
        <td class="smallText" width="40%" valign="top"><?php echo '<b>' . $value['title'] . '</b><br />' . $value['description']; ?></td>
        <td class="smallText" width="60%" valign="top"><?php echo $value_field; ?></td>
      </tr>
<?php
    }
?>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_UPDATE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'sDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
