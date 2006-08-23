<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require('../includes/classes/modules.php');

  $set = (isset($_GET['set']) ? $_GET['set'] : '');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  $file_extension = substr(__FILE__, strrpos(__FILE__, '.'));

  switch ($set) {
    case 'content':
      define('HEADING_TITLE', HEADING_TITLE_MODULES_CONTENT);
      define('TABLE_HEADING_MODULES_TITLE', TABLE_HEADING_MODULES_CONTENT);
      break;

    case 'boxes':
    default:
      $set = 'boxes';
      define('HEADING_TITLE', HEADING_TITLE_MODULES_BOXES);
      define('TABLE_HEADING_MODULES_TITLE', TABLE_HEADING_MODULES_BOXES);
      break;
  }

  $osC_Language->load('modules-' . $set);

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        if (isset($_POST['configuration']) && is_array($_POST['configuration'])) {
          $error = false;

          $osC_Database->startTransaction();

          foreach ($_POST['configuration'] as $key => $value) {
            $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
            $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
            $Qupdate->bindValue(':configuration_value', $value);
            $Qupdate->bindValue(':configuration_key', $key);
            $Qupdate->execute();

            if ($osC_Database->isError()) {
              $error = true;
              break;
            }
          }

          if ($error === false) {
            $osC_Database->commitTransaction();

            osC_Cache::clear('configuration');
          } else {
            $osC_Database->rollbackTransaction();
          }
        }

        tep_redirect(tep_href_link(FILENAME_TEMPLATES_BOXES, 'set=' . $set . '&' . $set . '=' . $_GET[$set]));
        break;
      case 'install':
      case 'remove':
        if (file_exists('../includes/modules/' . $set . '/' . $_GET[$set] . $file_extension)) {
          include('../includes/modules/' . $set . '/' . $_GET[$set] . $file_extension);
          $class = 'osC_' . ucfirst($set) . '_' . $_GET[$set];

          if (call_user_func(array($class, 'isInstalled'), $_GET[$set], $set) === false) {
            $osC_Language->injectDefinitions('modules/' . $set . '/' . $_GET[$set] . '.xml');
          }

          $module = new $class;
          if ($action == 'install') {
            $module->install();
          } elseif ($action == 'remove') {
            $module->remove();
          }
        }

        osC_Cache::clear('configuration');
        osC_Cache::clear('modules_' . $set);

        tep_redirect(tep_href_link(FILENAME_TEMPLATES_BOXES, 'set=' . $set . '&' . $set . '=' . $_GET[$set]));
        break;
    }
  }

  $page_contents = 'templates_boxes.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
