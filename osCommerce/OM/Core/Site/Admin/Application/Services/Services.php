<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Application_Services extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'services',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Language, $osC_MessageStack;

      $this->_page_title = $osC_Language->get('heading_title');

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      $this->_installed = explode(';', MODULE_SERVICES_INSTALLED);

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'save':
            $this->_page_contents = 'edit.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('configuration' => $_POST['configuration']);

              if ( $this->_save($data) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;

          case 'install':
            if ( $this->_install($_GET['module']) ) {
              $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
            } else {
              $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
            }

            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));

            break;

          case 'uninstall':
            $this->_page_contents = 'uninstall.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( $this->_uninstall($_GET['module']) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
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
        $Qsu = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
        $Qsu->bindTable(':table_configuration', TABLE_CONFIGURATION);
        $Qsu->bindValue(':configuration_value', $value);
        $Qsu->bindvalue(':configuration_key', $key);
        $Qsu->setLogging($_SESSION['module']);
        $Qsu->execute();

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

    function _install($module_key) {
      global $osC_Database;

      include('includes/modules/services/' . $module_key . '.php');

      $class = 'osC_Services_' . $module_key . '_Admin';

      $module = new $class();
      $module->install();

      if ( isset($module->depends) ) {
        if ( is_string($module->depends) && ( ( $key = array_search($module->depends, $this->_installed) ) !== false ) ) {
          if ( isset($this->_installed[$key+1]) ) {
            array_splice($this->_installed, $key+1, 0, $module_key);
          } else {
            $this->_installed[] = $module_key;
          }
        } elseif ( is_array($module->depends) ) {
          foreach ( $module->depends as $depends_module ) {
            if ( ( $key = array_search($depends_module, $this->_installed) ) !== false ) {
              if ( !isset($array_position) || ( $key > $array_position ) ) {
                $array_position = $key;
              }
            }
          }

          if ( isset($array_position) ) {
            array_splice($this->_installed, $array_position+1, 0, $module_key);
          } else {
            $this->_installed[] = $module_key;
          }
        }
      } elseif ( isset($module->precedes) ) {
        if ( is_string($module->precedes) ) {
          if ( ( $key = array_search($module->precedes, $this->_installed) ) !== false ) {
            array_splice($this->_installed, $key, 0, $module_key);
          } else {
            $this->_installed[] = $module_key;
          }
        } elseif ( is_array($module->precedes) ) {
          foreach ( $module->precedes as $precedes_module ) {
            if ( ( $key = array_search($precedes_module, $this->_installed) ) !== false ) {
              if ( !isset($array_position) || ( $key < $array_position ) ) {
                $array_position = $key;
              }
            }
          }

          if ( isset($array_position) ) {
            array_splice($this->_installed, $array_position, 0, $module_key);
          } else {
            $this->_installed[] = $module_key;
          }
        }
      } else {
        $this->_installed[] = $module_key;
      }

      $Qsu = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
      $Qsu->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qsu->bindValue(':configuration_value', implode(';', $this->_installed));
      $Qsu->bindValue(':configuration_key', 'MODULE_SERVICES_INSTALLED');
      $Qsu->execute();

      if ( !$osC_Database->isError() ) {
        osC_Cache::clear('configuration');

        return true;
      }

      return false;
    }

    function _uninstall($module_key) {
      global $osC_Database;

      include('includes/modules/services/' . $module_key . '.php');

      $class = 'osC_Services_' . $module_key . '_Admin';

      $module = new $class();
      $module->remove();

      unset($this->_installed[array_search($module_key, $this->_installed)]);

      $Qsu = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
      $Qsu->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qsu->bindValue(':configuration_value', implode(';', $this->_installed));
      $Qsu->bindValue(':configuration_key', 'MODULE_SERVICES_INSTALLED');
      $Qsu->execute();

      if ( !$osC_Database->isError() ) {
        osC_Cache::clear('configuration');

        return true;
      }

      return false;
    }
  }
?>
