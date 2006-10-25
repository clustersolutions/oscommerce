<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Services extends osC_Template {

/* Private variables */

    var $_module = 'services',
        $_page_title,
        $_page_contents = 'services.php';

/* Class constructor */

    function osC_Content_Services() {
      $this->_page_title = HEADING_TITLE;

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      $this->_installed = explode(';', MODULE_SERVICES_INSTALLED);

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
      global $osC_Database, $osC_MessageStack;

      if (isset($_POST['configuration' ]) && is_array($_POST['configuration']) && (sizeof($_POST['configuration']) > 0)) {
        $osC_Database->startTransaction();

        $error = false;
        $modified = false;

        foreach ($_POST['configuration'] as $key => $value) {
          $Qsu = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
          $Qsu->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qsu->bindValue(':configuration_value', $value);
          $Qsu->bindvalue(':configuration_key', $key);
          $Qsu->execute();

          if ($Qsu->affectedRows() && ($modified === false)) {
            $modified = true;
          }

          if ($osC_Database->isError()) {
            $error = true;
            break;
          }
        }

        if (($modified === true) && ($error === false)) {
          $osC_Database->commitTransaction();

          osC_Cache::clear('configuration');

          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_Database->rollbackTransaction();

          $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
        }
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&service=' . $_GET['service']));
    }

    function _install() {
      global $osC_Database, $osC_MessageStack;

      if (!array_search($_GET['service'], $this->_installed)) {
        include('../includes/services/' . $_GET['service'] . '.php');
        $class = 'osC_Services_' . $_GET['service'];
        $module = new $class();
        $module->install();

        if (isset($module->depends)) {
          if (is_string($module->depends) && (($key = array_search($module->depends, $this->_installed)) !== false)) {
            if (isset($this->_installed[$key+1])) {
              array_splice($this->_installed, $key+1, 0, $_GET['service']);
            } else {
              $this->_installed[] = $_GET['service'];
            }
          } elseif (is_array($module->depends)) {
            foreach ($module->depends as $depends_module) {
              if (($key = array_search($depends_module, $this->_installed)) !== false) {
                if (!isset($array_position) || ($key > $array_position)) {
                  $array_position = $key;
                }
              }
            }

            if (isset($array_position)) {
              array_splice($this->_installed, $array_position+1, 0, $_GET['service']);
            } else {
              $this->_installed[] = $_GET['service'];
            }
          }
        } elseif (isset($module->precedes)) {
          if (is_string($module->precedes)) {
            if ((($key = array_search($module->precedes, $this->_installed)) !== false)) {
              array_splice($this->_installed, $key, 0, $_GET['service']);
            } else {
              $this->_installed[] = $_GET['service'];
            }
          } elseif (is_array($module->precedes)) {
            foreach ($module->precedes as $precedes_module) {
              if (($key = array_search($precedes_module, $this->_installed)) !== false) {
                if (!isset($array_position) || ($key < $array_position)) {
                  $array_position = $key;
                }
              }
            }

            if (isset($array_position)) {
              array_splice($this->_installed, $array_position, 0, $_GET['service']);
            } else {
              $this->_installed[] = $_GET['service'];
            }
          }
        } else {
          $this->_installed[] = $_GET['service'];
        }

        $Qsu = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
        $Qsu->bindTable(':table_configuration', TABLE_CONFIGURATION);
        $Qsu->bindValue(':configuration_value', implode(';', $this->_installed));
        $Qsu->bindValue(':configuration_key', 'MODULE_SERVICES_INSTALLED');
        $Qsu->execute();

        if ($Qsu->affectedRows()) {
          osC_Cache::clear('configuration');

          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
        }
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&service=' . $_GET['service']));
    }

    function _remove() {
      global $osC_Database, $osC_MessageStack;

      if (($key = array_search($_GET['service'], $this->_installed)) !== false) {
        include('../includes/services/' . $_GET['service'] . '.php');
        $class = 'osC_Services_' . $_GET['service'];
        $module = new $class();
        $module->remove();

        unset($this->_installed[$key]);

        $Qsu = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
        $Qsu->bindTable(':table_configuration', TABLE_CONFIGURATION);
        $Qsu->bindValue(':configuration_value', implode(';', $this->_installed));
        $Qsu->bindValue(':configuration_key', 'MODULE_SERVICES_INSTALLED');
        $Qsu->execute();

        if ($Qsu->affectedRows()) {
          osC_Cache::clear('configuration');

          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
        }
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
    }
  }
?>
