<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/directory_listing.php');
  $osC_DirectoryListing = new osC_DirectoryListing('includes/modules/image');
  $osC_DirectoryListing->setIncludeDirectories(false);
  $osC_DirectoryListing->setCheckExtension('php');
  $directory_array = $osC_DirectoryListing->getFiles();

  $module_parameters = array();
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_iDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_MODULES; ?></th>
        <th width="100"><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>

<?php
  foreach ($directory_array as $file) {
    include('includes/modules/image/' . $file['name']);

    $class = 'osC_Image_Admin_' . substr($file['name'], 0, strrpos($file['name'], '.'));

    if (class_exists($class)) {
      $module = new $class();

      if ($module->hasParameters()) {
        $module_parameters[] = array('code' => $module->getModuleCode(),
                                     'title' => $module->getTitle(),
                                     'params' => $module->getParameters());
      }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td><?php echo $module->getTitle(); ?></td>
        <td align="right">
<?php
      if ($module->hasParameters()) {
        echo '<a href="#" onclick="toggleInfoBox(\'iEdit_' . $module->getModuleCode() . '\');">' . tep_image('templates/' . $template . '/images/icons/16x16/run.png', IMAGE_EXECUTE, '16', '16') . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_IMAGES, 'module=' . substr($file['name'], 0, strrpos($file['name'], '.'))) . '">' . tep_image('templates/' . $template . '/images/icons/16x16/run.png', IMAGE_EXECUTE, '16', '16') . '</a>';
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
  foreach ($module_parameters as $module) {
?>

<div id="<?php echo 'infoBox_iEdit_' . $module['code']; ?>" <?php if ($action != 'iEdit_' . $module['code']) { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $module['title']; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('iEdit', FILENAME_IMAGES, 'module=' . $module['code']); ?>

    <p><?php echo $module['title']; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
    foreach ($module['params'] as $params) {
?>

      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . $params['key'] . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo $params['field']; ?></td>
      </tr>

<?php
    }
?>

    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_EXECUTE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'iDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>