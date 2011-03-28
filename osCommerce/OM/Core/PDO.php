<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core;

  use \PDO;

  use osCommerce\OM\Core\OSCOM;

  class DatabasePDO extends PDO {
    protected $_connected = false;
    protected $_server;
    protected $_username;
    protected $_password;
    protected $_database;
    protected $_port;
    protected $_driver;
    protected $_driver_options = array();
    protected $_driver_parent;

    public static function initialize($server = null, $username = null, $password = null, $database = null, $port = null, $driver = null, $driver_options = array()) {
      if ( !isset($server) ) {
        $server = OSCOM::getConfig('db_server');
      }

      if ( !isset($username) && OSCOM::configExists('db_server_username')) {
        $username = OSCOM::getConfig('db_server_username');
      }

      if ( !isset($password) && OSCOM::configExists('db_server_password')) {
        $password = OSCOM::getConfig('db_server_password');
      }

      if ( !isset($database) && OSCOM::configExists('db_database')) {
        $database = OSCOM::getConfig('db_database');
      }

      if ( !isset($port) && OSCOM::configExists('db_server_port')) {
        $port = OSCOM::getConfig('db_server_port');
      }

      if ( !isset($driver) && OSCOM::configExists('db_driver')) {
        $driver = OSCOM::getConfig('db_driver');
      }

      if ( !isset($driver_options[PDO::ATTR_ERRMODE]) ) {
        $driver_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_WARNING;
      }

      if ( !isset($driver_options[PDO::ATTR_DEFAULT_FETCH_MODE]) ) {
        $driver_options[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_ASSOC;
      }

      if ( !isset($driver_options[PDO::ATTR_STATEMENT_CLASS]) ) {
        $driver_options[PDO::ATTR_STATEMENT_CLASS] = array('osCommerce\\OM\\Core\\DatabasePDOStatement');
      }

      $class = 'osCommerce\\OM\\Core\\DatabasePDO\\' . $driver;
      $object = new $class($server, $username, $password, $database, $port, $driver_options);

      $object->_driver = $driver;

      return $object;
    }

    public function exec($statement) {
      $statement = $this->_autoPrefixTables($statement);

      return parent::exec($statement);
    }

    public function prepare($statement, $driver_options = array()) {
      $statement = $this->_autoPrefixTables($statement);

      return parent::prepare($statement, $driver_options);
    }

    public function query($statement) {
      $statement = $this->_autoPrefixTables($statement);

      $args = func_get_args();

      if ( count($args) > 1 ) {
        return call_user_func_array(array($this, 'parent::query'), $args);
      } else {
        return parent::query($statement);
      }
    }

    public function getBatchFrom($pageset, $max_results) {
      return max(($pageset * $max_results) - $max_results, 0);
    }

    public function getDriver() {
      return $this->_driver;
    }

    public function getDriverParent() {
      return $this->_driver_parent;
    }

    public function hasDriverParent() {
      return isset($this->_driver_parent);
    }

    public function importSQL($sql_file, $table_prefix = null) {
      if ( file_exists($sql_file) ) {
        $import_queries = file_get_contents($sql_file);
      } else {
        trigger_error(sprintf(ERROR_SQL_FILE_NONEXISTENT, $sql_file));

        return false;
      }

      set_time_limit(0);

      $sql_queries = array();
      $sql_length = strlen($import_queries);
      $pos = strpos($import_queries, ';');

      for ( $i=$pos; $i<$sql_length; $i++ ) {
// remove comments
        if ( $import_queries[0] == '#' ) {
          $import_queries = ltrim(substr($import_queries, strpos($import_queries, "\n")));
          $sql_length = strlen($import_queries);
          $i = strpos($import_queries, ';')-1;
          continue;
        }

        if ( $import_queries[($i+1)] == "\n" ) {
          $next = '';

          for ( $j=($i+2); $j<$sql_length; $j++ ) {
            if ( !empty($import_queries[$j]) ) {
              $next = substr($import_queries, $j, 6);

              if ( $next[0] == '#' ) {
// find out where the break position is so we can remove this line (#comment line)
                for ( $k=$j; $k<$sql_length; $k++ ) {
                  if ( $import_queries[$k] == "\n" ) {
                    break;
                  }
                }

                $query = substr($import_queries, 0, $i+1);

                $import_queries = substr($import_queries, $k);

// join the query before the comment appeared, with the rest of the dump
                $import_queries = $query . $import_queries;
                $sql_length = strlen($import_queries);
                $i = strpos($import_queries, ';')-1;
                continue 2;
              }

              break;
            }
          }

          if ( empty($next) ) { // get the last insert query
            $next = 'insert';
          }

          if ( (strtoupper($next) == 'DROP T') || (strtoupper($next) == 'CREATE') || (strtoupper($next) == 'INSERT') || (strtoupper($next) == 'ALTER ') || (strtoupper($next) == 'SET FO') ) {
            $next = '';

            $sql_query = substr($import_queries, 0, $i);

            if ( isset($table_prefix) ) {
              if ( strtoupper(substr($sql_query, 0, 25)) == 'DROP TABLE IF EXISTS OSC_' ) {
                $sql_query = 'DROP TABLE IF EXISTS ' . $table_prefix . substr($sql_query, 25);
              } elseif ( strtoupper(substr($sql_query, 0, 17)) == 'CREATE TABLE OSC_' ) {
                $sql_query = 'CREATE TABLE ' . $table_prefix . substr($sql_query, 17);
              } elseif ( strtoupper(substr($sql_query, 0, 16)) == 'INSERT INTO OSC_' ) {
                $sql_query = 'INSERT INTO ' . $table_prefix . substr($sql_query, 16);
              }
            }

            $sql_queries[] = trim($sql_query);

            $import_queries = ltrim(substr($import_queries, $i+1));
            $sql_length = strlen($import_queries);
            $i = strpos($import_queries, ';')-1;
          }
        }
      }

      $error = false;

      foreach ( $sql_queries as $q ) {
        if ( $this->exec($q) === false ) {
          $error = true;

          break;
        }
      }

      return !$error;
    }

    protected function _autoPrefixTables($statement) {
      if ( OSCOM::configExists('db_table_prefix') ) {
        $statement = str_replace(':table_', OSCOM::getConfig('db_table_prefix'), $statement);
      }

      return $statement;
    }
  }
?>
