<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\DatabasePDO\MySQL;

  use \PDO;

  class Standard extends \osCommerce\OM\Core\DatabasePDO {
    public function __construct($server, $username, $password, $database, $port, $driver_options) {
      $this->_server = $server;
      $this->_username = $username;
      $this->_password = $password;
      $this->_database = $database;
      $this->_port = $port;
      $this->_driver_options = $driver_options;

// Only one init command can be issued (see http://bugs.php.net/bug.php?id=48859)
      $this->_driver_options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'set session sql_mode="STRICT_ALL_TABLES", names utf8';

      if ( $this->_connected === false ) {
        $this->connect();
      }
    }

    public function connect() {
      $dsn = 'mysql:dbname=' . $this->_database . ';host=' . $this->_server;

      if ( !empty($this->_port) ) {
        $dsn .= ';port=' . $this->_port;
      }

      parent::__construct($dsn, $this->_username, $this->_password, $this->_driver_options);

      $this->_connected = true;
    }
  }
?>
