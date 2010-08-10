<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\ServerInfo;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\DateTime;

  class ServerInfo {

/**
 * Retrieve web server and database server information
 *
 * @access public
 */

    public static function getAll() {
      $OSCOM_Database = Registry::get('Database');

      $result = array();

      $Qdate = $OSCOM_Database->query('select now() as datetime');
      $Quptime = $OSCOM_Database->query('show status like "Uptime"');

      @list($system, $host, $kernel) = preg_split('/[\s,]+/', @exec('uname -a'), 5);

      $db_uptime = intval($Quptime->valueInt('Value') / 3600) . ':' . str_pad(intval(($Quptime->valueInt('Value') / 60) % 60), 2, '0', STR_PAD_LEFT);

      $data = array(array('key' => 'date',
                          'title' => OSCOM::getDef('field_server_date'),
                          'value' => DateTime::getShort(null, true)),
                    array('key' => 'system',
                          'title' => OSCOM::getDef('field_server_operating_system'),
                          'value' => $system . ' ' . $kernel),
                    array('key' => 'host',
                          'title' => OSCOM::getDef('field_server_host'),
                          'value' => $host . ' (' . gethostbyname($host) . ')'),
                    array('key' => 'uptime',
                          'title' => OSCOM::getDef('field_server_up_time'),
                          'value' => @exec('uptime')),
                    array('key' => 'http_server',
                          'title' => OSCOM::getDef('field_http_server'),
                          'value' => $_SERVER['SERVER_SOFTWARE']),
                    array('key' => 'php',
                          'title' => OSCOM::getDef('field_php_version'),
                          'value' => 'PHP v' . PHP_VERSION . ' / Zend v' . zend_version()),
                    array('key' => 'db_server',
                          'title' => OSCOM::getDef('field_database_host'),
                          'value' => DB_SERVER . ' (' . gethostbyname(DB_SERVER) . ')'),
                    array('key' => 'db_version',
                          'title' => OSCOM::getDef('field_database_version'),
                          'value' => 'MySQL v' . $OSCOM_Database->getServerVersion()),
                    array('key' => 'db_date',
                          'title' => OSCOM::getDef('field_database_date'),
                          'value' => DateTime::getShort($Qdate->value('datetime'), true)),
                    array('key' => 'db_uptime',
                          'title' => OSCOM::getDef('field_database_up_time'),
                          'value' => $db_uptime));

      $result['entries'] = $data;

      $result['total'] = count($data);

      return $result;
    }

    public static function find($search) {
      $modules = self::getAll();

      $result = array('entries' => array());

      foreach ( $modules['entries'] as $module ) {
        if ( (stripos($module['key'], $search) !== false) || (stripos($module['title'], $search) !== false) || (stripos($module['value'], $search) !== false) ) {
          $result['entries'][] = $module;
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
