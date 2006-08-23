<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  $file_extension = substr(__FILE__, strrpos(__FILE__, '.'));

  if (!empty($action)) {
    switch ($action) {
      case 'save':
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

        tep_redirect(osc_href_link_admin(FILENAME_TEMPLATES, 'template=' . $_GET['template']));
        break;
      case 'install':
      case 'remove':
        if (file_exists('includes/templates/' . $_GET['template'] . $file_extension)) {
          include('includes/templates/' . $_GET['template'] . $file_extension);
          $class = 'osC_Template_' . $_GET['template'];
          $module = new $class();
          if ($action == 'install') {
            $module->install();
          } elseif ($action == 'remove') {
            $module->remove();
          }
        }

        osC_Cache::clear('configuration');
        osC_Cache::clear('templates');

        tep_redirect(osc_href_link_admin(FILENAME_TEMPLATES, 'template=' . $_GET['template']));
        break;
    }
  }

  $page_contents = 'templates.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
