<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  $installed = explode(';', MODULE_SERVICES_INSTALLED);

  $file_extension = substr(__FILE__, strrpos(__FILE__, '.'));

  if (!empty($action)) {
    switch ($action) {
      case 'save':
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

            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_Database->rollbackTransaction();

            $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        }

        tep_redirect(tep_href_link(FILENAME_SERVICES, 'service=' . $_GET['service']));
        break;
      case 'remove':
        if (($key = array_search($_GET['service'], $installed)) !== false) {
          include('../includes/services/' . $_GET['service'] . $file_extension);
          $class = 'osC_Services_' . $_GET['service'];
          $module = new $class;
          $module->remove();

          unset($installed[$key]);

          $Qsu = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
          $Qsu->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qsu->bindValue(':configuration_value', implode(';', $installed));
          $Qsu->bindValue(':configuration_key', 'MODULE_SERVICES_INSTALLED');
          $Qsu->execute();

          if ($Qsu->affectedRows()) {
            osC_Cache::clear('configuration');

            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        }

        tep_redirect(tep_href_link(FILENAME_SERVICES, 'service=' . $_GET['service']));
        break;
      case 'install':
        if (array_search($_GET['service'], $installed) === false) {
          include('../includes/services/' . $_GET['service'] . $file_extension);
          $class = 'osC_Services_' . $_GET['service'];
          $module = new $class;
          $module->install();

          if (isset($module->depends)) {
            if (is_string($module->depends) && (($key = array_search($module->depends, $installed)) !== false)) {
              if (isset($installed[$key+1])) {
                array_splice($installed, $key+1, 0, $_GET['service']);
              } else {
                $installed[] = $_GET['service'];
              }
            } elseif (is_array($module->depends)) {
              foreach ($module->depends as $depends_module) {
                if (($key = array_search($depends_module, $installed)) !== false) {
                  if (!isset($array_position) || ($key > $array_position)) {
                    $array_position = $key;
                  }
                }
              }

              if (isset($array_position)) {
                array_splice($installed, $array_position+1, 0, $_GET['service']);
              } else {
                $installed[] = $_GET['service'];
              }
            }
          } elseif (isset($module->preceeds)) {
            if (is_string($module->preceeds)) {
              if ((($key = array_search($module->preceeds, $installed)) !== false)) {
                array_splice($installed, $key, 0, $_GET['service']);
              } else {
                $installed[] = $_GET['service'];
              }
            } elseif (is_array($module->preceeds)) {
              foreach ($module->preceeds as $preceeds_module) {
                if (($key = array_search($preceeds_module, $installed)) !== false) {
                  if (!isset($array_position) || ($key < $array_position)) {
                    $array_position = $key;
                  }
                }
              }

              if (isset($array_position)) {
                array_splice($installed, $array_position, 0, $_GET['service']);
              } else {
                $installed[] = $_GET['service'];
              }
            }
          } else {
            $installed[] = $_GET['service'];
          }

          $Qsu = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
          $Qsu->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qsu->bindValue(':configuration_value', implode(';', $installed));
          $Qsu->bindValue(':configuration_key', 'MODULE_SERVICES_INSTALLED');
          $Qsu->execute();

          if ($Qsu->affectedRows()) {
            osC_Cache::clear('configuration');

            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        }

        tep_redirect(tep_href_link(FILENAME_SERVICES, 'service=' . $_GET['service']));
        break;
    }
  }

  $page_contents = 'services.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
