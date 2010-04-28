<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_ErrorHandler {
    static protected $_resource;

    public static function initialize() {
      if ( class_exists('SQLite3', false) ) {
        set_error_handler(array('OSCOM_ErrorHandler', 'execute'), E_STRICT);
      }
    }

    public static function execute($errno, $errstr, $errfile, $errline) {
      if ( !is_resource(self::$_resource) ) {
        self::connect();
      }

      switch ($errno) {
        case E_NOTICE:
        case E_USER_NOTICE:
          $errors = "Notice";
          break;
        case E_WARNING:
        case E_USER_WARNING:
          $errors = "Warning";
          break;
        case E_ERROR:
        case E_USER_ERROR:
          $errors = "Fatal Error";
          break;
        default:
          $errors = "Unknown";
          break;
      }

      $error_msg = sprintf('PHP %s:  %s in %s on line %d', $errors, $errstr, $errfile, $errline);

      self::$_resource->exec('insert into error_log (timestamp, message) values (' . time() . ', "' . $error_msg . '");');

      if ( (int)ini_get('display_errors') > 0 ) {
        echo sprintf('<br />' . "\n" . '<b>%s</b>: %s in <b>%s</b> on line <b>%d</b><br /><br />' . "\n", $errors, $errstr, $errfile, $errline);
      }

      if ( (int)ini_get('log_errors') > 0 ) {
        error_log($error_msg);
      }
    }

    public static function connect() {
      self::$_resource = new SQLite3(OSCOM::BASE_DIRECTORY . 'work/oscommerce.sqlite3');
      self::$_resource->exec('create table if not exists error_log ( timestamp int, message text );');
    }

    public static function getAll($limit = null, $offset = null) {
      if ( !is_resource(self::$_resource) ) {
        self::connect();
      }

      $result = array();

      $query = 'select timestamp, message from error_log order by timestamp desc';

      if ( !empty($limit) ) {
        $query .= ' limit ' . (int)$limit;
      }

      $Qlogs = self::$_resource->query($query);

      while ( $row = $Qlogs->fetchArray(SQLITE3_ASSOC) ) {
        $result[] = $row;
      }

      return $result;
    }
  }
?>
