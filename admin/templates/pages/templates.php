<?php
/*
  $Id: modules.php 241 2005-11-13 22:56:32Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/directory_listing.php');
  $osC_DirectoryListing = new osC_DirectoryListing('includes/templates');
  $osC_DirectoryListing->setIncludeDirectories(false);
  $files = $osC_DirectoryListing->getFiles();
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_tDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_TEMPLATES; ?></th>
        <th><?php echo TABLE_HEADING_STATUS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>

<?php
  foreach ($files as $file) {
    include('includes/templates/' . $file['name']);

    $code = substr($file['name'], 0, strrpos($file['name'], '.'));
    $class = 'osC_Template_' . $code;

    if (class_exists($class)) {
      $module = new $class();

      if (!isset($tInfo) && (!isset($_GET['template']) || (isset($_GET['template']) && ($_GET['template'] == $code)))) {
        $template_info = array('code' => $module->getCode(),
                               'title' => $module->getTitle(),
                               'author_name' => $module->getAuthorName(),
                               'author_www' => $module->getAuthorAddress(),
                               'markup' => $module->getMarkup(),
                               'css_based' => ($module->isCSSBased() ? 'Yes' : 'No'),
                               'medium' => $module->getMedium(),
                               'installed' => $module->isInstalled());

        $keys_extra = array();
        foreach ($module->getKeys() as $key) {
          $Qkeys = $osC_Database->query('select configuration_title, configuration_value, configuration_description, use_function, set_function from :table_configuration where configuration_key = :configuration_key');
          $Qkeys->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qkeys->bindValue(':configuration_key', $key);
          $Qkeys->execute();

          $keys_extra[$key]['title'] = $Qkeys->value('configuration_title');
          $keys_extra[$key]['value'] = $Qkeys->value('configuration_value');
          $keys_extra[$key]['description'] = $Qkeys->value('configuration_description');
          $keys_extra[$key]['use_function'] = $Qkeys->value('use_function');
          $keys_extra[$key]['set_function'] = $Qkeys->value('set_function');
        }

        $template_info['keys'] = $keys_extra;

        $tInfo = new objectInfo($template_info);
      }

      if (isset($tInfo) && ($code == $tInfo->code) ) {
        echo '      <tr class="selected">' . "\n";
      } else {
        echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_TEMPLATES, 'template=' . $code) . '\';">' . "\n";
      }
?>

        <td>

<?php
      if ($module->getCode() == DEFAULT_TEMPLATE) {
        echo '<b>' . $module->getTitle() . ' <i>(' . TEXT_DEFAULT . ')</i></b>';
      } else {
        echo $module->getTitle();
      }
?>

        </td>
        <td align="center"><?php echo osc_icon(($module->isInstalled() ? ($module->isActive() ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif') : 'checkbox.gif'), null, null); ?></td>
        <td align="right">
<?php
      if (isset($tInfo) && ($code == $tInfo->code)) {
        if ($tInfo->installed === true) {
          echo '<a href="#" onclick="toggleInfoBox(\'tInfo\');">' . osc_icon('info.png', IMAGE_INFO) . '</a>&nbsp;' .
               '<a href="#" onclick="toggleInfoBox(\'tUninstall\');">' . osc_icon('stop.png', IMAGE_MODULE_REMOVE) . '</a>&nbsp;';

          if ($module->hasKeys() || ($module->getCode() != DEFAULT_TEMPLATE)) {
            echo '<a href="#" onclick="toggleInfoBox(\'tEdit\');">' . osc_icon('configure.png', IMAGE_EDIT) . '</a>';
          } else {
            echo osc_image('images/pixel_trans.gif', '', '16', '16');
          }
        } else {
          echo osc_link_object(osc_href_link_admin(FILENAME_TEMPLATES, 'template=' . $code . '&action=tInfo'), osc_icon('info.png', IMAGE_INFO)) . '&nbsp;' .
               osc_link_object(osc_href_link_admin(FILENAME_TEMPLATES, 'template=' . $code . '&action=install'), osc_icon('play.png', IMAGE_MODULE_INSTALL)) . '&nbsp;' .
               osc_image('images/pixel_trans.gif', '', '16', '16');
        }
      } else {
        if ($module->isInstalled() && $module->isActive()) {
          echo osc_link_object(osc_href_link_admin(FILENAME_TEMPLATES, 'template=' . $code . '&action=tInfo'), osc_icon('info.png', IMAGE_INFO)) . '&nbsp;' .
               osc_link_object(osc_href_link_admin(FILENAME_TEMPLATES, 'template=' . $code . '&action=tUninstall'), osc_icon('stop.png', IMAGE_MODULE_REMOVE)) . '&nbsp;';

          if ($module->hasKeys() || ($module->getCode() != DEFAULT_TEMPLATE)) {
            echo osc_link_object(osc_href_link_admin(FILENAME_TEMPLATES, 'template=' . $code . '&action=tEdit'), osc_icon('configure.png', IMAGE_EDIT));
          } else {
            echo osc_image('images/pixel_trans.gif', '', '16', '16');
          }
        } else {
          echo osc_link_object(osc_href_link_admin(FILENAME_TEMPLATES, 'template=' . $code . '&action=tInfo'), osc_icon('info.png', IMAGE_INFO)) . '&nbsp;' .
               osc_link_object(osc_href_link_admin(FILENAME_TEMPLATES, 'template=' . $code . '&action=install'), osc_icon('play.png', IMAGE_MODULE_INSTALL)) . '&nbsp;' .
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
  if (isset($tInfo)) {
?>

<div id="infoBox_tInfo" <?php if ($action != 'tInfo') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('info.png', IMAGE_INFO) . ' ' . $tInfo->title; ?></div>
  <div class="infoBoxContent">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>Title:</td>
        <td><?php echo $tInfo->title; ?></td>
      </tr>
      <tr>
        <td>Author:</td>
        <td><?php echo $tInfo->author_name; ?> (<?php echo $tInfo->author_www; ?>)</td>
      </tr>
      <tr>
        <td>Markup:</td>
        <td><?php echo $tInfo->markup; ?></td>
      </tr>
      <tr>
        <td>CSS Based:</td>
        <td><?php echo $tInfo->css_based; ?></td>
      </tr>
      <tr>
        <td>Presentation Medium:</td>
        <td><?php echo $tInfo->medium; ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" class="operationButton" onclick="toggleInfoBox(\'tDefault\');">'; ?></p>
  </div>
</div>

<div id="infoBox_tUninstall" <?php if ($action != 'tUninstall') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('stop.png', IMAGE_MODULE_REMOVE) . ' ' . $tInfo->title; ?></div>
  <div class="infoBoxContent">
<?php
    if ($tInfo->code == DEFAULT_TEMPLATE) {
?>
    <p><?php echo '<b>' . TEXT_INFO_DELETE_PROHIBITED . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="toggleInfoBox(\'tDefault\');" class="operationButton">'; ?></p>
<?php
    } else {
?>
    <p><?php echo INFO_TEMPLATE_UNINSTALL_INTRO; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_MODULE_REMOVE . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_TEMPLATES, 'template=' . $tInfo->code . '&action=remove') . '\';"> <input type="button" value="' . IMAGE_CANCEL . '" class="operationButton" onclick="toggleInfoBox(\'tDefault\');">'; ?></p>
<?php
    }
?>
  </div>
</div>

<div id="infoBox_tEdit" <?php if ($action != 'tEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $tInfo->title; ?></div>
  <div class="infoBoxContent">
    <form name="tEdit" action="<?php echo osc_href_link_admin(FILENAME_TEMPLATES, 'template=' . $tInfo->code . '&action=save'); ?>" method="post">

<?php
    $keys = '';
    foreach ($tInfo->keys as $key => $value) {
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

<?php
    if ($tInfo->code != DEFAULT_TEMPLATE) {
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_SET_DEFAULT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
      </tr>
    </table>
<?php
    }
?>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'tDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
