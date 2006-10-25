<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $directory_array = array();
  if ($dir = @dir('includes/modules/statistics')) {
    while ($file = $dir->read()) {
      if (!is_dir('includes/modules/statistics/' . $file)) {
        if (substr($file, strrpos($file, '.')) == '.php') {
          $directory_array[] = $file;
        }
      }
    }
    sort($directory_array);
    $dir->close();
  }
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo TABLE_HEADING_MODULES; ?></th>
    </tr>
  </thead>
  <tbody>

<?php
  $installed_modules = array();
  foreach ($directory_array as $file) {
    include('includes/modules/statistics/' . $file);

    $class = 'osC_Statistics_' . str_replace(' ', '_', ucwords(str_replace('_', ' ', substr($file, 0, strrpos($file, '.')))));
    if (class_exists($class)) {
      $module = new $class;
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . substr($file, 0, strrpos($file, '.'))), $module->getIcon() . '&nbsp;' . $module->getTitle()); ?></td>
    </tr>

<?php
    }
  }
?>

  </tbody>
</table>

<p><?php echo TEXT_MODULE_DIRECTORY . ' ' . realpath(dirname(__FILE__) . '/../../includes/modules/statistics'); ?></p>
