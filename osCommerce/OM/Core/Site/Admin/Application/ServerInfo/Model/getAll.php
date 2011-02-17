<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\ServerInfo\Model;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\DateTime;

  class getAll {
    public static function execute() {
      $result = array();

      $db_time = OSCOM::callDB('Admin\ServerInfo\GetTime');
      $db_uptime = OSCOM::callDB('Admin\ServerInfo\GetUptime');
      $db_version = OSCOM::callDB('Admin\ServerInfo\GetVersion');

      @list($system, $host, $kernel) = preg_split('/[\s,]+/', @exec('uname -a'), 5);

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
                          'value' => $db_version),
                    array('key' => 'db_date',
                          'title' => OSCOM::getDef('field_database_date'),
                          'value' => DateTime::getShort($db_time, true)),
                    array('key' => 'db_uptime',
                          'title' => OSCOM::getDef('field_database_up_time'),
                          'value' => $db_uptime));

      $result['entries'] = $data;

      $result['total'] = count($data);

      return $result;
    }
  }
?>
