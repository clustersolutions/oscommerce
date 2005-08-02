<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

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

<h1><?php echo HEADING_TITLE; ?></h1>

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
    <tr onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);">
      <td><?php echo '<a href="' . tep_href_link(FILENAME_STATISTICS, 'module=' . substr($file, 0, strrpos($file, '.'))) . '">' . $module->getIcon() . '&nbsp;' . $module->getTitle() . '</a>'; ?></td>
    </tr>
<?php
    }
  }
?>
  </tbody>
</table>

<p><?php echo TEXT_MODULE_DIRECTORY . ' ' . realpath(dirname(__FILE__) . '/../../includes/modules/statistics'); ?></p>
