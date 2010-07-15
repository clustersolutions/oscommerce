<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Setup\Application\Install;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Database;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\DirectoryListing;

  class RPC {
    public static function dbCheck() {
      $db = array('DB_SERVER' => trim(urldecode($_POST['server'])),
                  'DB_SERVER_USERNAME' => trim(urldecode($_POST['username'])),
                  'DB_SERVER_PASSWORD' => trim(urldecode($_POST['password'])),
                  'DB_DATABASE' => trim(urldecode($_POST['name'])),
                  'DB_SERVER_PORT' => trim(urldecode($_POST['port'])),
                  'DB_DATABASE_CLASS' => trim(urldecode(str_replace('_', '\\', $_POST['class'])))
                 );

      Registry::set('Database', Database::initialize($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD'], $db['DB_DATABASE'], $db['DB_SERVER_PORT'], $db['DB_DATABASE_CLASS']));
      Registry::set('osC_Database', Registry::get('Database')); // HPDL to delete

      $OSCOM_Database = Registry::get('Database');

      if ( !$OSCOM_Database->isError() ) {
        $OSCOM_Database->selectDatabase($db['DB_DATABASE']);
      }

      if ( !$OSCOM_Database->isError() ) {
        echo '[[1]]';
      } else {
        echo '[[0|' . $OSCOM_Database->getError() . ']]';
      }
    }

    public static function dbImport() {
      $db = array('DB_SERVER' => trim(urldecode($_POST['server'])),
                  'DB_SERVER_USERNAME' => trim(urldecode($_POST['username'])),
                  'DB_SERVER_PASSWORD' => trim(urldecode($_POST['password'])),
                  'DB_DATABASE' => trim(urldecode($_POST['name'])),
                  'DB_SERVER_PORT' => trim(urldecode($_POST['port'])),
                  'DB_DATABASE_CLASS' => trim(urldecode(str_replace('_', '\\', $_POST['class']))),
                  'DB_INSERT_SAMPLE_DATA' => ((trim(urldecode($_POST['import'])) == '1') ? 'true' : 'false'),
                  'DB_TABLE_PREFIX' => trim(urldecode($_POST['prefix']))
                 );

      Registry::set('Database', Database::initialize($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD'], $db['DB_DATABASE'], $db['DB_SERVER_PORT'], $db['DB_DATABASE_CLASS']));
      Registry::set('osC_Database', Registry::get('Database')); // HPDL to delete

      $OSCOM_Database = Registry::get('Database');

      if ( !$OSCOM_Database->isError() ) {
        $sql_file = OSCOM::BASE_DIRECTORY . 'sites/Setup/sql/oscommerce.sql';

        $OSCOM_Database->importSQL($sql_file, $db['DB_DATABASE'], $db['DB_TABLE_PREFIX']);
      }

      if ( !$OSCOM_Database->isError() ) {
        define('DB_TABLE_PREFIX', $db['DB_TABLE_PREFIX']); // HPDL to remove
        include(OSCOM::BASE_DIRECTORY . 'database_tables.php'); // HPDL to remove

        foreach ( Registry::get('Language')->extractDefinitions('en_US.xml') as $def ) {
          $Qdef = $OSCOM_Database->query('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
          $Qdef->bindInt(':languages_id', 1);
          $Qdef->bindValue(':content_group', $def['group']);
          $Qdef->bindValue(':definition_key', $def['key']);
          $Qdef->bindValue(':definition_value', $def['value']);
          $Qdef->execute();
        }

        $OSCOM_DirectoryListing = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'languages/en_US');
        $OSCOM_DirectoryListing->setRecursive(true);
        $OSCOM_DirectoryListing->setIncludeDirectories(false);
        $OSCOM_DirectoryListing->setAddDirectoryToFilename(true);
        $OSCOM_DirectoryListing->setCheckExtension('xml');

        foreach ( $OSCOM_DirectoryListing->getFiles() as $files ) {
          foreach ( Registry::get('Language')->extractDefinitions('en_US/' . $files['name']) as $def ) {
            $Qdef = $OSCOM_Database->query('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
            $Qdef->bindInt(':languages_id', 1);
            $Qdef->bindValue(':content_group', $def['group']);
            $Qdef->bindValue(':definition_key', $def['key']);
            $Qdef->bindValue(':definition_value', $def['value']);
            $Qdef->execute();
          }
        }
      }

      if ( !$OSCOM_Database->isError() ) {
        $services = array('OutputCompression',
                          'Session',
                          'Language',
                          'Debug',
                          'Currencies',
                          'Core',
                          'SimpleCounter',
                          'CategoryPath',
                          'Breadcrumb',
                          'WhosOnline',
  //                        'banner',
                          'Specials',
                          'Reviews',
                          'RecentlyVisited');

        $installed = array();

        foreach ( $services as $service ) {
          include(OSCOM::BASE_DIRECTORY . 'sites/Admin/includes/modules/services/' . $service . '.php');
          $class = 'osC_Services_' . $service . '_Admin';
          $module = new $class();
          $module->install();

          if ( isset($module->depends) ) {
            if ( is_string($module->depends) && (($key = array_search($module->depends, $installed)) !== false) ) {
              if ( isset($installed[$key+1]) ) {
                array_splice($installed, $key+1, 0, $service);
              } else {
                $installed[] = $service;
              }
            } elseif ( is_array($module->depends) ) {
              foreach ( $module->depends as $depends_module ) {
                if ( ($key = array_search($depends_module, $installed)) !== false ) {
                  if ( !isset($array_position) || ($key > $array_position) ) {
                    $array_position = $key;
                  }
                }
              }

              if ( isset($array_position) ) {
                array_splice($installed, $array_position+1, 0, $service);
              } else {
                $installed[] = $service;
              }
            }
          } elseif ( isset($module->precedes) ) {
            if ( is_string($module->precedes) ) {
              if ( ($key = array_search($module->precedes, $installed)) !== false ) {
                array_splice($installed, $key, 0, $service);
              } else {
                $installed[] = $service;
              }
            } elseif ( is_array($module->precedes) ) {
              foreach ( $module->precedes as $precedes_module ) {
                if ( ($key = array_search($precedes_module, $installed)) !== false ) {
                  if ( !isset($array_position) || ($key < $array_position) ) {
                    $array_position = $key;
                  }
                }
              }

              if ( isset($array_position) ) {
                array_splice($installed, $array_position, 0, $service);
              } else {
                $installed[] = $service;
              }
            }
          } else {
            $installed[] = $service;
          }

          unset($array_position);
        }

        $Qs = $OSCOM_Database->query('insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ("Service Modules", "MODULE_SERVICES_INSTALLED",  :configuration_value, "Installed services modules", "6", "0", now())');
        $Qs->bindValue(':configuration_value', implode(';', $installed));
        $Qs->execute();

        include(OSCOM::BASE_DIRECTORY . 'sites/Admin/classes/payment.php');
        include(OSCOM::BASE_DIRECTORY . 'sites/Admin/classes/shipping.php');
        include(OSCOM::BASE_DIRECTORY . 'sites/Admin/classes/order_total.php');

        define('DEFAULT_ORDERS_STATUS_ID', 1); // HPDL to remove
        include(OSCOM::BASE_DIRECTORY . 'sites/Admin/includes/modules/payment/cod.php');
        $module = new \osC_Payment_cod(); // HPDL
        $module->install();

        $Qupdate = $OSCOM_Database->query('update :table_configuration set configuration_value = 1 where configuration_key = :configuration_key');
        $Qupdate->bindValue(':configuration_key', 'MODULE_PAYMENT_COD_STATUS');
        $Qupdate->execute();

        include(OSCOM::BASE_DIRECTORY . 'sites/Admin/includes/modules/shipping/flat.php');
        $module = new \osC_Shipping_flat(); // HPDL
        $module->install();

        include(OSCOM::BASE_DIRECTORY . 'sites/Admin/includes/modules/order_total/sub_total.php');
        $module = new \osC_OrderTotal_sub_total(); // HPDL
        $module->install();

        include(OSCOM::BASE_DIRECTORY . 'sites/Admin/includes/modules/order_total/shipping.php');
        $module = new \osC_OrderTotal_shipping(); // HPDL
        $module->install();

        include(OSCOM::BASE_DIRECTORY . 'sites/Admin/includes/modules/order_total/tax.php');
        $module = new \osC_OrderTotal_tax(); // HPDL
        $module->install();

        include(OSCOM::BASE_DIRECTORY . 'sites/Admin/includes/modules/order_total/total.php');
        $module = new \osC_OrderTotal_total(); // HPDL
        $module->install();
      }

      if ( !$OSCOM_Database->isError() && ($db['DB_DATABASE_CLASS'] == 'mysql_innodb') ) {
        $Qinno = $OSCOM_Database->query('show variables like "have_innodb"');

        if ( ($Qinno->numberOfRows() === 1) && (strtolower($Qinno->value('Value')) == 'yes') ) {
          $innodb_sql_file = OSCOM::BASE_DIRECTORY . 'sites/Setup/sql/oscommerce_innodb.sql';

          $OSCOM_Database->importSQL($innodb_sql_file, $db['DB_DATABASE'], $db['DB_TABLE_PREFIX']);
        }
      }

      if ( !$OSCOM_Database->isError() ) {
        echo '[[1]]';
      } else {
        echo '[[0|' . $OSCOM_Database->getError() . ']]';
      }
    }

    public static function dbImportSample() {
      $db = array('DB_SERVER' => trim(urldecode($_POST['server'])),
                  'DB_SERVER_USERNAME' => trim(urldecode($_POST['username'])),
                  'DB_SERVER_PASSWORD' => trim(urldecode($_POST['password'])),
                  'DB_DATABASE' => trim(urldecode($_POST['name'])),
                  'DB_SERVER_PORT' => trim(urldecode($_POST['port'])),
                  'DB_DATABASE_CLASS' => trim(urldecode(str_replace('_', '\\', $_POST['class']))),
                  'DB_TABLE_PREFIX' => trim(urldecode($_POST['prefix']))
                 );

      Registry::set('Database', Database::initialize($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD'], $db['DB_DATABASE'], $db['DB_SERVER_PORT'], $db['DB_DATABASE_CLASS']));
      Registry::set('osC_Database', Registry::get('Database')); // HPDL to delete

      $OSCOM_Database = Registry::get('Database');

      if ( !$OSCOM_Database->isError() ) {
        $sql_file = OSCOM::BASE_DIRECTORY . 'sites/Setup/sql/oscommerce_sample_data.sql';

        $OSCOM_Database->importSQL($sql_file, $db['DB_DATABASE'], $db['DB_TABLE_PREFIX']);
      }

      if ( !$OSCOM_Database->isError() ) {
        echo '[[1]]';
      } else {
        echo '[[0|' . $OSCOM_Database->getError() . ']]';
      }
    }
  }
?>
