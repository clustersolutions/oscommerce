<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Modules_shipping extends osC_Template {

/* Private variables */

    var $_module = 'modules_shipping',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Modules_shipping() {
      global $osC_MessageStack;

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      include('includes/classes/shipping.php');

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'save':
            $this->_page_contents = 'edit.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('configuration' => $_POST['configuration']);

              if ( $this->_save($data) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
              }

              osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;

          case 'install':
            if ( $this->_install($_GET['module']) ) {
              $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
            } else {
              $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
            }

            osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));

            break;

          case 'uninstall':
            $this->_page_contents = 'uninstall.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( $this->_uninstall($_GET['module']) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
              }

              osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;
        }
      }
    }

/* Private methods */

    function _save($data) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      foreach ( $data['configuration'] as $key => $value ) {
        $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
        $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
        $Qupdate->bindValue(':configuration_value', is_array($data['configuration'][$key]) ? implode(',', $data['configuration'][$key]) : $value);
        $Qupdate->bindValue(':configuration_key', $key);
        $Qupdate->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('configuration');

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    function _install($key) {
      global $osC_Database, $osC_Language;

      if ( file_exists('includes/modules/shipping/' . $key . '.php') ) {
        $osC_Language->injectDefinitions('modules/shipping/' . $key . '.xml');

        include('includes/modules/shipping/' . $key . '.php');

        $module = 'osC_Shipping_' . $key;
        $module = new $module();

        $module->install();

        osC_Cache::clear('modules-shipping');
        osC_Cache::clear('configuration');

        return true;
      }

      return false;
    }

    function _uninstall($key) {
      global $osC_Database, $osC_Language;

      if ( file_exists('includes/modules/shipping/' . $key . '.php') ) {
        $osC_Language->injectDefinitions('modules/shipping/' . $key . '.xml');

        include('includes/modules/shipping/' . $key . '.php');

        $module = 'osC_Shipping_' . $key;
        $module = new $module();

        $module->remove();

        osC_Cache::clear('modules-shipping');
        osC_Cache::clear('configuration');

        return true;
      }

      return false;
    }
  }
?>
