<?php
/*
  $Id: modules.php,v 1.49 2004/07/22 22:43:47 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $selected_box = 'modules';

  $set = (isset($_GET['set']) ? $_GET['set'] : '');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));

  if (!empty($set)) {
    switch ($set) {
      case 'shipping':
        $module_type = 'shipping';
        $module_key = 'MODULE_SHIPPING_INSTALLED';
        define('HEADING_TITLE', HEADING_TITLE_MODULES_SHIPPING);
        break;
      case 'ordertotal':
        $module_type = 'order_total';
        $module_key = 'MODULE_ORDER_TOTAL_INSTALLED';
        define('HEADING_TITLE', HEADING_TITLE_MODULES_ORDER_TOTAL);
        break;
      case 'payment':
      default:
        $module_type = 'payment';
        $module_key = 'MODULE_PAYMENT_INSTALLED';
        define('HEADING_TITLE', HEADING_TITLE_MODULES_PAYMENT);
        break;
    }
  }

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

        tep_redirect(tep_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $_GET['module']));
        break;
      case 'install':
      case 'remove':
        if (file_exists('../includes/modules/' . $module_type . '/' . $_GET['module'] . $file_extension)) {
          include('../includes/languages/' . $osC_Session->value('language') . '/modules/' . $module_type . '/' . $_GET['module'] . $file_extension);
          include('../includes/modules/' . $module_type . '/' . $_GET['module'] . $file_extension);
          $module = new $_GET['module'];
          if ($action == 'install') {
            $module->install();
          } elseif ($action == 'remove') {
            $module->remove();
          }
        }

        osC_Cache::clear('configuration');

        tep_redirect(tep_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $_GET['module']));
        break;
    }
  }

  $page_contents = 'modules.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
