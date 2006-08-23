<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $set = (isset($_GET['set']) ? $_GET['set'] : '');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  $file_extension = substr(__FILE__, strrpos(__FILE__, '.'));

  if (!empty($set)) {
    switch ($set) {
      case 'shipping':
        $module_type = 'shipping';
        $module_class = 'osC_Shipping_';
        define('HEADING_TITLE', HEADING_TITLE_MODULES_SHIPPING);
        include('includes/classes/shipping.php');
        break;
      case 'ordertotal':
        $module_type = 'order_total';
        $module_class = 'osC_OrderTotal_';
        define('HEADING_TITLE', HEADING_TITLE_MODULES_ORDER_TOTAL);
        include('includes/classes/order_total.php');
        break;
      case 'payment':
      default:
        $module_type = 'payment';
        $module_class = 'osC_Payment_';
        define('HEADING_TITLE', HEADING_TITLE_MODULES_PAYMENT);
        include('includes/classes/payment.php');
        break;
    }
  }

  $osC_Language->load('modules-' . $module_type);

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        if (isset($_POST['configuration']) && is_array($_POST['configuration'])) {
          $error = false;

          $osC_Database->startTransaction();

          foreach ($_POST['configuration'] as $key => $value) {
            $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
            $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
            $Qupdate->bindValue(':configuration_value', is_array($_POST['configuration'][$key]) ? implode(',', $_POST['configuration'][$key]) : $value);
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

        tep_redirect(osc_href_link_admin(FILENAME_MODULES, 'set=' . $set . '&module=' . $_GET['module']));
        break;
      case 'install':
      case 'remove':
        if (file_exists('includes/modules/' . $module_type . '/' . $_GET['module'] . $file_extension)) {
          $osC_Language->injectDefinitions('modules/' . $module_type . '/' .$_GET['module'] . '.xml');
          include('includes/modules/' . $module_type . '/' . $_GET['module'] . $file_extension);
          $module = $module_class . $_GET['module'];
          $module = new $module();
          if ($action == 'install') {
            $module->install();
          } elseif ($action == 'remove') {
            $module->remove();
          }
        }

        osC_Cache::clear('modules-' , $module_type);
        osC_Cache::clear('configuration');

        tep_redirect(osc_href_link_admin(FILENAME_MODULES, 'set=' . $set . '&module=' . $_GET['module']));
        break;
    }
  }

  $page_contents = 'modules.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
