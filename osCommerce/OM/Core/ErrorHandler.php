<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core;

  class ErrorHandler {
    static protected $_resource;

    public static function initialize() {
      if ( class_exists('SQLite3', false) && is_writable(OSCOM::BASE_DIRECTORY . 'Work/Logs') ) {
        ini_set('display_errors', false);
        ini_set('log_errors', true);
        ini_set('error_log', OSCOM::BASE_DIRECTORY . 'Work/Logs/errors.txt');

        set_error_handler(array('osCommerce\\OM\\Core\\ErrorHandler', 'execute'));

        if ( file_exists(OSCOM::BASE_DIRECTORY . 'Work/Logs/errors.txt') ) {
          self::import(OSCOM::BASE_DIRECTORY . 'Work/Logs/errors.txt');
        }
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

// return true to stop further processing of internal php error handler
      return true;
    }

    public static function connect() {
      self::$_resource = new \SQLite3(OSCOM::BASE_DIRECTORY . 'Work/Database/errors.sqlite3');
      self::$_resource->exec('create table if not exists error_log ( timestamp int, message text );');
    }

    public static function getAll($limit = null, $pageset = null) {
      if ( !is_resource(self::$_resource) ) {
        self::connect();
      }

      $result = array();

      $query = 'select timestamp, message from error_log order by rowid desc';

      if ( is_numeric($limit) ) {
        $query .= ' limit ' . (int)$limit;

        if ( is_numeric($pageset) ) {
          $offset = max(($pageset * $limit) - $limit, 0);

          $query .= ' offset ' . $offset;
        }
      }

      $Qlogs = self::$_resource->query($query);

      while ( $row = $Qlogs->fetchArray(SQLITE3_ASSOC) ) {
        $result[] = $row;
      }

      return $result;
    }

    public static function getTotalEntries() {
      if ( !is_resource(self::$_resource) ) {
        self::connect();
      }

      return self::$_resource->querySingle('select count(*) from error_log');
    }

    public static function find($search, $limit = null, $offset = null, $from = 0) {
      if ( !is_resource(self::$_resource) ) {
        self::connect();
      }

      $result = array();

      $query = 'select timestamp, message from error_log where message like "%' . addslashes($search) . '%" and timestamp > '.$from.' order by rowid desc';

      if ( !empty($limit) ) {
        $query .= ' limit ' . (int)$limit;
      }

      $Qlogs = self::$_resource->query($query);

      while ( $row = $Qlogs->fetchArray(SQLITE3_ASSOC) ) {
        $result[] = $row;
      }

      return $result;
    }

    public static function getTotalFindEntries($search) {
      if ( !is_resource(self::$_resource) ) {
        self::connect();
      }

      return self::$_resource->querySingle('select count(*) from error_log where message like "%' . addslashes($search) . '%"');
    }

    public static function import($filename) {
      $error_log = file($filename);
      unlink($filename);

      if ( !is_resource(self::$_resource) ) {
        self::connect();
      }

      foreach ( $error_log as $error ) {
        if ( preg_match('/^\[([0-9]{2})-([A-Za-z]{3})-([0-9]{4}) ([0-9]{2}):([0-5][0-9]):([0-5][0-9])\] (.*)$/', $error) ) {
          $timestamp = DateTime::getTimestamp(substr($error, 1, 20), 'd-M-Y H:i:s');
          $message = substr($error, 23);

          self::$_resource->exec('insert into error_log (timestamp, message) values (' . $timestamp . ', "' . $message . '");');
        }
      }
    }

    public static function clear() {
      if ( !is_resource(self::$_resource) ) {
        self::connect();
      }

      self::$_resource->exec('drop table if exists error_log');
    }
  }
?>
