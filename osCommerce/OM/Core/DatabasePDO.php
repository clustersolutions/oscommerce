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
    protected $_has_native_fk = false;
    protected $_fkeys = array();

    public static function initialize($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $port = DB_SERVER_PORT, $driver = DB_DATABASE_CLASS, $driver_options = array()) {
      $driver = 'MySQL\\V5'; // HPDL REMOVE

      if ( !isset($driver_options[PDO::ATTR_DEFAULT_FETCH_MODE]) ) {
        $driver_options[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_ASSOC;
      }

      $class = 'osCommerce\\OM\\Core\\DatabasePDO\\' . $driver;
      $object = new $class($server, $username, $password, $database, $port, $driver_options);

      $object->_driver = $driver;
      $object->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('osCommerce\\OM\\Core\\DatabasePDOStatement'));

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

    public function hasNativeForeignKeys() {
      return $this->_has_native_fk;
    }

    public function hasForeignKeys() {
      return !empty($this->_fkeys);
    }

    public function getForeignKeys($table = null) {
      if ( isset($table) ) {
        return $this->_fkeys[$table];
      }

      return $this->_fkeys;
    }

    public function setupForeignKeys() {
      if ( empty($this->_fkeys) ) {
        $Qfk = self::prepare('select * from :table_fk_relationships');
//        $Qfk->setCache('fk_relationships');
        $Qfk->execute();

        while ( $Qfk->next() ) {
          $this->_fkeys[$Qfk->value('to_table')][] = array('from_table' => $Qfk->value('from_table'),
                                                           'from_field' => $Qfk->value('from_field'),
                                                           'to_field' => $Qfk->value('to_field'),
                                                           'on_update' => $Qfk->value('on_update'),
                                                           'on_delete' => $Qfk->value('on_delete'));
        }

//        $Qfk->freeResult();
      }
    }

    public function hasForeignKey($table) {
      return isset($this->_fkeys[$table]);
    }

    protected function _autoPrefixTables($statement) {
      return str_replace(':table_', DB_TABLE_PREFIX, $statement);
    }
  }
?>
