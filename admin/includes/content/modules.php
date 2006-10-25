<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Modules extends osC_Template {

/* Private variables */

    var $_module = 'modules',
        $_page_title,
        $_page_contents = 'modules.php';

/* Class constructor */

    function osC_Content_Modules() {
      global $osC_Language;

      if (!isset($_GET['set'])) {
        $_GET['set'] = '';
      }

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      switch ($_GET['set']) {
        case 'shipping':
          $this->_module_type = 'shipping';
          $this->_module_class = 'osC_Shipping_';
          $this->_page_title = HEADING_TITLE_MODULES_SHIPPING;
          include('includes/classes/shipping.php');
          break;

        case 'ordertotal':
          $this->_module_type = 'order_total';
          $this->_module_class = 'osC_OrderTotal_';
          $this->_page_title = HEADING_TITLE_MODULES_ORDER_TOTAL;
          include('includes/classes/order_total.php');
          break;

        case 'payment':
        default:
          $this->_module_type = 'payment';
          $this->_module_class = 'osC_Payment_';
          $this->_page_title = HEADING_TITLE_MODULES_PAYMENT;
          include('includes/classes/payment.php');
          break;
      }

      $osC_Language->load('modules-' . $this->_module_type);

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'save':
            $this->_save();
            break;

          case 'install':
            $this->_install();
            break;

          case 'remove':
            $this->_remove();
            break;
        }
      }
    }

/* Private methods */

    function _save() {
      global $osC_Database;

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

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $this->_module_type . '&module=' . $_GET['module']));
    }

    function _install() {
      global $osC_Database, $osC_Language;

      if (file_exists('includes/modules/' . $this->_module_type . '/' . $_GET['module'] . '.php')) {
        $osC_Language->injectDefinitions('modules/' . $this->_module_type . '/' .$_GET['module'] . '.xml');
        include('includes/modules/' . $this->_module_type . '/' . $_GET['module'] . '.php');
        $module = $this->_module_class . $_GET['module'];
        $module = new $module();
        $module->install();
      }

      osC_Cache::clear('modules-' , $this_>_module_type);
      osC_Cache::clear('configuration');

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $this->_module_type . '&module=' . $_GET['module']));
    }

    function _remove() {
      global $osC_Database, $osC_Language;

      if (file_exists('includes/modules/' . $this->_module_type . '/' . $_GET['module'] . '.php')) {
        $osC_Language->injectDefinitions('modules/' . $this->_module_type . '/' .$_GET['module'] . '.xml');
        include('includes/modules/' . $this->_module_type . '/' . $_GET['module'] . '.php');
        $module = $this->_module_class . $_GET['module'];
        $module = new $module();
        $module->remove();
      }

      osC_Cache::clear('modules-' , $this_>_module_type);
      osC_Cache::clear('configuration');

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $this->_module_type));
    }
  }
?>
