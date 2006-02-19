<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

  require('includes/application.php');

  $dir_fs_www_root = dirname(__FILE__);

  if (isset($_GET['action']) && !empty($_GET['action'])) {
    switch ($_GET['action']) {
      case 'dbCheck':
        $db = array('DB_SERVER' => trim(urldecode($_GET['server'])),
                    'DB_SERVER_USERNAME' => trim(urldecode($_GET['username'])),
                    'DB_SERVER_PASSWORD' => trim(urldecode($_GET['password'])),
                    'DB_DATABASE' => trim(urldecode($_GET['name'])),
                    'DB_DATABASE_CLASS' => trim(urldecode($_GET['class']))
                   );

        $osC_Database = osC_Database::connect($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD'], $db['DB_DATABASE_CLASS']);

        if ($osC_Database->isError() === false) {
          $osC_Database->selectDatabase($db['DB_DATABASE']);
        }

        if ($osC_Database->isError()) {
          echo '0:osCRPC:' . $osC_Database->getError();
        } else {
          echo '1';
        }

        exit;
        break;

      case 'dbImport':
        $db = array('DB_SERVER' => trim(urldecode($_GET['server'])),
                    'DB_SERVER_USERNAME' => trim(urldecode($_GET['username'])),
                    'DB_SERVER_PASSWORD' => trim(urldecode($_GET['password'])),
                    'DB_DATABASE' => trim(urldecode($_GET['name'])),
                    'DB_DATABASE_CLASS' => trim(urldecode($_GET['class'])),
                    'DB_INSERT_SAMPLE_DATA' => ((trim(urldecode($_GET['import'])) == '1') ? 'true' : 'false'),
                    'DB_TABLE_PREFIX' => trim(urldecode($_GET['prefix']))
                   );

        $osC_Database = osC_Database::connect($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD'], $db['DB_DATABASE_CLASS']);

        if ($osC_Database->isError() === false) {
          $osC_Database->selectDatabase($db['DB_DATABASE']);
        }

        if ($osC_Database->isError() === false) {
          $sql_file = $dir_fs_www_root . '/oscommerce.sql';

          $osC_Database->importSQL($sql_file, $db['DB_DATABASE'], $db['DB_TABLE_PREFIX']);
        }

        if ( ($osC_Database->isError() === false) && ($db['DB_INSERT_SAMPLE_DATA'] == 'true') ) {
          $sql_file = $dir_fs_www_root . '/oscommerce_sample_data.sql';

          $osC_Database->importSQL($sql_file, $db['DB_DATABASE'], $db['DB_TABLE_PREFIX']);
        }

        if ($osC_Database->isError() === false) {
          include('../includes/classes/xml.php');
          include('../admin/includes/classes/directory_listing.php');

          foreach ($osC_Language->extractDefinitions('en_US.xml') as $def) {
            $Qdef = $osC_Database->query('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
            $Qdef->bindTable(':table_languages_definitions', $db['DB_TABLE_PREFIX'] . 'languages_definitions');
            $Qdef->bindInt(':languages_id', 1);
            $Qdef->bindValue(':content_group', $def['group']);
            $Qdef->bindValue(':definition_key', $def['key']);
            $Qdef->bindValue(':definition_value', $def['value']);
            $Qdef->execute();
          }

          $osC_DirectoryListing = new osC_DirectoryListing('../includes/languages/en_US');
          $osC_DirectoryListing->setRecursive(true);
          $osC_DirectoryListing->setIncludeDirectories(false);
          $osC_DirectoryListing->setAddDirectoryToFilename(true);
          $osC_DirectoryListing->setCheckExtension('xml');

          foreach ($osC_DirectoryListing->getFiles() as $files) {
            foreach ($osC_Language->extractDefinitions('en_US/' . $files['name']) as $def) {
              $Qdef = $osC_Database->query('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
              $Qdef->bindTable(':table_languages_definitions', $db['DB_TABLE_PREFIX'] . 'languages_definitions');
              $Qdef->bindInt(':languages_id', 1);
              $Qdef->bindValue(':content_group', $def['group']);
              $Qdef->bindValue(':definition_key', $def['key']);
              $Qdef->bindValue(':definition_value', $def['value']);
              $Qdef->execute();
            }
          }
        }

        if ($osC_Database->isError() === false) {
          define('DB_TABLE_PREFIX', $db['DB_TABLE_PREFIX']);
          include('../includes/database_tables.php');
          include('includes/classes/payment.php');
          include('includes/classes/shipping.php');
          include('includes/classes/order_total.php');

          include('../includes/modules/payment/cod.php');
          $module = new osC_Payment_cod();
          $module->install();

          include('../includes/modules/payment/cc.php');
          $module = new osC_Payment_cc();
          $module->install();

          include('../includes/modules/shipping/flat.php');
          $module = new osC_Shipping_flat();
          $module->install();

          include('../includes/modules/order_total/sub_total.php');
          $module = new osC_OrderTotal_sub_total();
          $module->install();

          include('../includes/modules/order_total/shipping.php');
          $module = new osC_OrderTotal_shipping();
          $module->install();

          include('../includes/modules/order_total/tax.php');
          $module = new osC_OrderTotal_tax();
          $module->install();

          include('../includes/modules/order_total/total.php');
          $module = new osC_OrderTotal_total();
          $module->install();
        }

        if ( ($osC_Database->isError() === false) && ($db['DB_DATABASE_CLASS'] == 'mysql_innodb') ) {
          $Qinno = $osC_Database->query('show variables like "have_innodb"');
          if (($Qinno->numberOfRows() === 1) && (strtolower($Qinno->value('Value')) == 'yes')) {
            $database_tables = array('address_book', 'categories', 'categories_description', 'customers', 'customers_basket', 'customers_basket_attributes', 'customers_info', 'manufacturers', 'manufacturers_info', 'orders', 'orders_products', 'orders_status', 'orders_status_history', 'orders_products_attributes', 'orders_products_download', 'orders_total', 'products', 'products_attributes', 'products_attributes_download', 'products_description', 'products_options', 'products_options_values', 'products_options_values_to_products_options', 'products_to_categories', 'reviews', 'weight_classes', 'weight_classes_rules');

            foreach ($database_tables as $table) {
              $osC_Database->simpleQuery('alter table ' . $db['DB_TABLE_PREFIX'] . $table . ' type = innodb');
            }
          }
        }

        if ($osC_Database->isError()) {
          echo '0:osCRPC:' . $osC_Database->getError();
        } else {
          echo '1';
        }

        exit;
        break;

      case 'dbImportSample':
        $db = array('DB_SERVER' => trim(urldecode($_GET['server'])),
                    'DB_SERVER_USERNAME' => trim(urldecode($_GET['username'])),
                    'DB_SERVER_PASSWORD' => trim(urldecode($_GET['password'])),
                    'DB_DATABASE' => trim(urldecode($_GET['name'])),
                    'DB_DATABASE_CLASS' => trim(urldecode($_GET['class'])),
                    'DB_TABLE_PREFIX' => trim(urldecode($_GET['prefix']))
                   );

        $osC_Database = osC_Database::connect($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD'], $db['DB_DATABASE_CLASS']);

        if ($osC_Database->isError() === false) {
          $osC_Database->selectDatabase($db['DB_DATABASE']);
        }

        if ($osC_Database->isError() === false) {
          $sql_file = $dir_fs_www_root . '/oscommerce_sample_data.sql';

          $osC_Database->importSQL($sql_file, $db['DB_DATABASE'], $db['DB_TABLE_PREFIX']);
        }

        if ($osC_Database->isError()) {
          echo '0:osCRPC:' . $osC_Database->getError();
        } else {
          echo '1';
        }

        exit;
        break;

      case 'checkWorkDir':
        $directory = trim(urldecode($_GET['dir']));

        if (file_exists($directory)) {
          if (is_writeable($directory)) {
            if (file_exists($directory . '/.htaccess') === false) {
              if ($fp = @fopen($directory . '/.htaccess', 'w')) {
                flock($fp, 2); // LOCK_EX
                fputs($fp, "<Files *>\nOrder Deny,Allow\nDeny from all\n</Files>");
                flock($fp, 3); // LOCK_UN
                fclose($fp);
              }
            }

            echo '1';
          } else {
            echo '0:osCRPC:' . $directory;
          }
        } else {
          echo '-1:osCRPC:' . $directory;
        }

        exit;
        break;

      case 'getDirectoryPath':
        $directory = trim(urldecode($_GET['dir']));

        if (!is_dir($directory) || (false === $fh = @opendir($directory))) {
          $query = basename($directory);
          $directory = dirname($directory);

          if ($fh = @opendir($directory)) {
            $dirs = array();
            while (false !== ($dir = readdir($fh))) {
              if ( ($dir != '.') && ($dir != '..') && (substr($dir, 0, 1) != '.') && is_dir($directory . '/' . $dir)) {
                if (strlen($query) > 1) {
                  if (substr($dir, 0, strlen($query)) == $query) {
                    $dirs[] = $directory . '/' . $dir;
                  }
                } else {
                  $dirs[] = $directory . '/' . $dir;
                }
              }
            }
            closedir($fh);

            if (sizeof($dirs) > 0) {
              sort($dirs);

              echo '0:osCRPC:' . implode('|', $dirs);
            } else {
              echo '-1:osCRPC:invalidPath';
            }
          } else {
            echo '-1:osCRPC:invalidPath';
          }
        } else {
          echo '1:osCRPC:' . $directory;
        }

        exit;
        break;
    }
  }

  echo '-100:osCRPC:noActionError';
?>
