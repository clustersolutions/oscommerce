<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Templates_modules extends osC_Template {

/* Private variables */

    var $_module = 'templates_modules',
        $_page_title,
        $_page_contents = 'templates_modules.php';

/* Class constructor */

    function osC_Content_Templates_modules() {
      global $osC_Language;

      if (!isset($_GET['set'])) {
        $_GET['set'] = '';
      }

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      switch ($_GET['set']) {
        case 'content':
          $this->_page_title = HEADING_TITLE_MODULES_CONTENT;
          break;

        case 'boxes':
        default:
          $_GET['set'] = 'boxes';
          $this->_page_title = HEADING_TITLE_MODULES_BOXES;
          break;
      }

      include('../includes/classes/modules.php');

      $osC_Language->load('modules-' . $_GET['set']);

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

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set'] . '&module=' . $_GET['module']));
    }

    function _install() {
      global $osC_Language;

      if (file_exists('../includes/modules/' . $_GET['set'] . '/' . $_GET['module'] . '.php')) {
        include('../includes/modules/' . $_GET['set'] . '/' . $_GET['module'] . '.php');
        $class = 'osC_' . ucfirst($_GET['set']) . '_' . $_GET['module'];

        if (call_user_func(array($class, 'isInstalled'), $_GET['module'], $_GET['set']) === false) {
          $osC_Language->injectDefinitions('modules/' . $_GET['set'] . '/' . $_GET['module'] . '.xml');
        }

        $module = new $class();
        $module->install();
      }

      osC_Cache::clear('configuration');
      osC_Cache::clear('modules_' . $_GET['set']);
      osC_Cache::clear('templates_' . $_GET['set'] . '_layout');

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set'] . '&module=' . $_GET['module']));
    }

    function _remove() {
      global $osC_Language;

      if (file_exists('../includes/modules/' . $_GET['set'] . '/' . $_GET['module'] . '.php')) {
        include('../includes/modules/' . $_GET['set'] . '/' . $_GET['module'] . '.php');
        $class = 'osC_' . ucfirst($_GET['set']) . '_' . $_GET['module'];

        if (call_user_func(array($class, 'isInstalled'), $_GET['module'], $_GET['set']) === false) {
          $osC_Language->injectDefinitions('modules/' . $_GET['set'] . '/' . $_GET['module'] . '.xml');
        }

        $module = new $class();
        $module->remove();
      }

      osC_Cache::clear('configuration');
      osC_Cache::clear('modules_' . $_GET['set']);
      osC_Cache::clear('templates_' . $_GET['set'] . '_layout');

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set']));
    }
  }
?>
