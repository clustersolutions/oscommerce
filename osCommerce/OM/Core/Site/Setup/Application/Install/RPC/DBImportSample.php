<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Setup\Application\Install\RPC;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Database;
  use osCommerce\OM\Core\OSCOM;

  class DBImportSample {
    public static function execute() {
      $db = array('DB_SERVER' => trim(urldecode($_POST['server'])),
                  'DB_SERVER_USERNAME' => trim(urldecode($_POST['username'])),
                  'DB_SERVER_PASSWORD' => trim(urldecode($_POST['password'])),
                  'DB_DATABASE' => trim(urldecode($_POST['name'])),
                  'DB_SERVER_PORT' => trim(urldecode($_POST['port'])),
                  'DB_DATABASE_CLASS' => trim(urldecode(str_replace('_', '\\', $_POST['class']))),
                  'DB_TABLE_PREFIX' => trim(urldecode($_POST['prefix']))
                 );

      Registry::set('Database', Database::initialize($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD'], $db['DB_DATABASE'], $db['DB_SERVER_PORT'], $db['DB_DATABASE_CLASS']));

      $OSCOM_Database = Registry::get('Database');

      if ( !$OSCOM_Database->isError() ) {
        $sql_file = OSCOM::BASE_DIRECTORY . 'Core/Site/Setup/sql/oscommerce_sample_data.sql';

        $OSCOM_Database->importSQL($sql_file, $db['DB_DATABASE'], $db['DB_TABLE_PREFIX']);
      }

      if ( !$OSCOM_Database->isError() ) {
        $result = array('result' => true);
      } else {
        $result = array('result' => false,
                        'error_message' => $OSCOM_Database->getError());
      }

      echo json_encode($result);
    }
  }
?>
