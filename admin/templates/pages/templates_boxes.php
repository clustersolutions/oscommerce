<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/directory_listing.php');

  $osC_DirectoryListing = new osC_DirectoryListing('../includes/modules/' . $set);
  $osC_DirectoryListing->setIncludeDirectories(false);
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_mDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_MODULES_TITLE; ?></th>
        <th><?php echo TABLE_HEADING_STATUS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>

<?php
  foreach ($osC_DirectoryListing->getFiles() as $file) {
    include('../includes/modules/' . $set . '/' . $file['name']);

    $code = substr($file['name'], 0, strrpos($file['name'], '.'));
    $class = 'osC_' . ucfirst($set) . '_' . $code;

   if (class_exists($class)) {
      if (call_user_func(array($class, 'isInstalled'), $code, $set) === false) {
        $osC_Language->injectDefinitions('modules/' . $set . '/' . $code . '.xml');
      }

      $module = new $class();

      if (!isset($mInfo) && (!isset($_GET[$set]) || (isset($_GET[$set]) && ($_GET[$set] == $code)))) {
        $info = array('code' => $module->getCode(),
                      'title' => $module->getTitle(),
                      'author_name' => $module->getAuthorName(),
                      'author_www' => $module->getAuthorAddress(),
                      'installed' => $module->isInstalled());

        if ($module->hasKeys()) {
          $keys = array();

          $Qkeys = $osC_Database->query('select configuration_title, configuration_key, configuration_value, configuration_description, use_function, set_function from :table_configuration where configuration_key in (:configuration_key)');
          $Qkeys->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qkeys->bindRaw(':configuration_key', "'" . implode("', '", $module->getKeys()) . "'");
          $Qkeys->execute();

          while ($Qkeys->next()) {
            $keys[$Qkeys->value('configuration_key')]['title'] = $Qkeys->value('configuration_title');
            $keys[$Qkeys->value('configuration_key')]['value'] = $Qkeys->value('configuration_value');
            $keys[$Qkeys->value('configuration_key')]['description'] = $Qkeys->value('configuration_description');
            $keys[$Qkeys->value('configuration_key')]['use_function'] = $Qkeys->value('use_function');
            $keys[$Qkeys->value('configuration_key')]['set_function'] = $Qkeys->value('set_function');
          }

          $info['keys'] = $keys;
        }

        $mInfo = new objectInfo($info);
      }

      if (isset($mInfo) && ($code == $mInfo->code) ) {
        echo '      <tr class="selected">' . "\n";
      } else {
        echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . tep_href_link(FILENAME_TEMPLATES_BOXES, 'set=' . $set . '&' . $set . '=' . $code) . '\';">' . "\n";
      }
?>
        <td><?php echo $module->getTitle(); ?></td>
        <td align="center"><?php echo tep_image('templates/' . $template . '/images/icons/' . ($module->isInstalled() ? ($module->isActive() ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif') : 'checkbox.gif')); ?></td>
        <td align="right">
<?php
    if (isset($mInfo) && ($code == $mInfo->code)) {
      echo '<a href="#" onclick="toggleInfoBox(\'mInfo\');">' . tep_image('templates/' . $template . '/images/icons/16x16/info.png', IMAGE_INFO, '16', '16') . '</a>&nbsp;';

      if ($mInfo->installed === true) {
        echo '<a href="#" onclick="toggleInfoBox(\'mUninstall\');">' . tep_image('templates/' . $template . '/images/icons/16x16/stop.png', IMAGE_MODULE_REMOVE, '16', '16') . '</a>&nbsp;';

        if ($module->hasKeys()) {
          echo '<a href="#" onclick="toggleInfoBox(\'mEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>';
        } else {
          echo tep_image('images/pixel_trans.gif', '', '16', '16');
        }
      } else {
        echo '<a href="' . tep_href_link(FILENAME_TEMPLATES_BOXES, 'set=' . $set . '&' . $set . '=' . $code . '&action=install') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/play.png', IMAGE_MODULE_INSTALL, '16', '16') . '</a>&nbsp;' .
             tep_image('images/pixel_trans.gif', '', '16', '16');
      }
    } else {
      echo '<a href="' . tep_href_link(FILENAME_TEMPLATES_BOXES, 'set=' . $set . '&' . $set . '=' . $code . '&action=mInfo') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/info.png', IMAGE_INFO, '16', '16') . '</a>&nbsp;';

      if ($module->isInstalled() && $module->isActive()) {
        echo '<a href="' . tep_href_link(FILENAME_TEMPLATES_BOXES, 'set=' . $set . '&' . $set . '=' . $code . '&action=mUninstall') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/stop.png', IMAGE_MODULE_REMOVE, '16', '16') . '</a>&nbsp;';

        if ($module->hasKeys()) {
          echo '<a href="' . tep_href_link(FILENAME_TEMPLATES_BOXES, 'set=' . $set . '&' . $set . '=' . $code . '&action=mEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>';
        } else {
          echo tep_image('images/pixel_trans.gif', '', '16', '16');
        }
      } else {
        echo '<a href="' . tep_href_link(FILENAME_TEMPLATES_BOXES, 'set=' . $set . '&' . $set . '=' . $code . '&action=install') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/play.png', IMAGE_MODULE_INSTALL, '16', '16') . '</a>&nbsp;' .
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

  <p><?php echo TEXT_DIRECTORY . ' ' . $osC_DirectoryListing->getDirectory(); ?></p>
</div>

<?php
  if (isset($mInfo)) {
?>

<div id="infoBox_mInfo" <?php if ($action != 'mInfo') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/info.png', IMAGE_INFO, '16', '16') . ' ' . $mInfo->title; ?></div>
  <div class="infoBoxContent">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>Title:</td>
        <td><?php echo $mInfo->title; ?></td>
      </tr>
      <tr>
        <td>Author:</td>
        <td><?php echo $mInfo->author_name; ?> (<?php echo $mInfo->author_www; ?>)</td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" class="operationButton" onclick="toggleInfoBox(\'mDefault\');">'; ?></p>
  </div>
</div>

<div id="infoBox_mUninstall" <?php if ($action != 'mUninstall') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/stop.png', IMAGE_MODULE_REMOVE, '16', '16') . ' ' . $mInfo->title; ?></div>
  <div class="infoBoxContent">
    <p><?php echo INFO_UNINSTALL_INTRO; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_MODULE_REMOVE . '" class="operationButton" onclick="document.location.href=\'' . tep_href_link(FILENAME_TEMPLATES_BOXES, 'set=' . $set . '&' . $set . '=' . $mInfo->code . '&action=remove') . '\';"> <input type="button" value="' . IMAGE_CANCEL . '" class="operationButton" onclick="toggleInfoBox(\'mDefault\');">'; ?></p>
  </div>
</div>

<div id="infoBox_mEdit" <?php if ($action != 'mEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $mInfo->title; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('mEdit', FILENAME_TEMPLATES_BOXES, 'set=' . $set . '&' . $set . '=' . $mInfo->code . '&action=save'); ?>

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
