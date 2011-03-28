<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\PDO;

  class SQLite3 extends \osCommerce\OM\Core\PDO {
    public function __construct($server, $username, $password, $database, $port, $driver_options) {
      $this->_server = $server;
      $this->_username = $username;
      $this->_password = $password;
      $this->_database = $database;
      $this->_port = $port;
      $this->_driver_options = $driver_options;

      return $this->connect();
    }

    public function connect() {
      $dsn = 'sqlite:' . $this->_server;

      $this->_connected = true;

      return parent::__construct($dsn, $this->_username, $this->_password, $this->_driver_options);
    }
  }
?>
