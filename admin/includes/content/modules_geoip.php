<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Modules_geoip extends osC_Template {

/* Private variables */

    var $_module = 'modules_geoip',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'modules_geoip.php';

/* Class constructor */

    function osC_Content_Modules_geoip() {
      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      include('includes/classes/geoip.php');

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

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&module=' . $_GET['module']));
    }

    function _install() {
      global $osC_Database, $osC_Language;

      if (file_exists('includes/modules/geoip/' . $_GET['module'] . '.php')) {
//        $osC_Language->injectDefinitions('modules/geoip/' .$_GET['module'] . '.xml');
        include('includes/modules/geoip/' . $_GET['module'] . '.php');
        $module = 'osC_GeoIP_' . $_GET['module'];
        $module = new $module();
        $module->install();
      }

      osC_Cache::clear('modules-geoip');
      osC_Cache::clear('configuration');

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&module=' . $_GET['module']));
    }

    function _remove() {
      global $osC_Database, $osC_Language;

      if (file_exists('includes/modules/geoip/' . $_GET['module'] . '.php')) {
//        $osC_Language->injectDefinitions('modules/geoip/' .$_GET['module'] . '.xml');
        include('includes/modules/geoip/' . $_GET['module'] . '.php');
        $module = 'osC_GeoIP_' . $_GET['module'];
        $module = new $module();
        $module->remove();
      }

      osC_Cache::clear('modules-geoip');
      osC_Cache::clear('configuration');

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
    }
  }
?>
