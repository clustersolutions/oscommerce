<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Templates extends osC_Template {

/* Private variables */

    var $_module = 'templates',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'templates.php';

/* Class constructor */

    function osC_Content_Templates() {
      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'save':
            $this->_save();
            break;

          case 'install':
            $this->_install();
            break;

          case 'remove':
            $this->_delete();
            break;
        }
      }
    }

/* Private methods */

    function _save() {
      global $osC_Database;

      $default = (isset($_POST['default']) && ($_POST['default'] == 'on')) ? true : false;

      if (isset($_POST['configuration']) && is_array($_POST['configuration'])) {
        $error = false;

        $osC_Database->startTransaction();

        if ($default === true) {
          $_POST['configuration']['DEFAULT_TEMPLATE'] = $_GET['template'];
        }

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
      } elseif ($default === true) {
        $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
        $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
        $Qupdate->bindValue(':configuration_value', $_GET['template']);
        $Qupdate->bindValue(':configuration_key', 'DEFAULT_TEMPLATE');
        $Qupdate->execute();

        osC_Cache::clear('configuration');
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&template=' . $_GET['template']));
    }

    function _install() {
      if (file_exists('includes/templates/' . $_GET['template'] . '.php')) {
        include('includes/templates/' . $_GET['template'] . '.php');

        $class = 'osC_Template_' . $_GET['template'];
        $module = new $class();

        $module->install();
      }

      osC_Cache::clear('configuration');
      osC_Cache::clear('templates');

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&template=' . $_GET['template']));
    }

    function _delete() {
      if (file_exists('includes/templates/' . $_GET['template'] . '.php')) {
        include('includes/templates/' . $_GET['template'] . '.php');

        $class = 'osC_Template_' . $_GET['template'];
        $module = new $class();

        $module->remove();
      }

      osC_Cache::clear('configuration');
      osC_Cache::clear('templates');

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&template=' . $_GET['template']));
    }
  }
?>
