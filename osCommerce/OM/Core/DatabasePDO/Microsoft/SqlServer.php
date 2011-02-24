<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\DatabasePDO\Microsoft;

  use \PDO;

  class SqlServer extends \osCommerce\OM\Core\DatabasePDO {
    public function __construct($server, $username, $password, $database, $port, $driver_options) {
      $this->_server = $server;
      $this->_username = $username;
      $this->_password = $password;
      $this->_database = $database;
      $this->_port = $port;
      $this->_driver_options = $driver_options;

      if ( $this->_connected === false ) {
        $this->connect();
      }
    }

    public function connect() {
      $dsn = 'sqlsrv:Server=' . $this->_server;

      if ( !empty($this->_port) ) {
        $dsn .= ', ' . $this->_port;
      }

      $dsn .= '; Database=' . $this->_database;

      parent::__construct($dsn, $this->_username, $this->_password, $this->_driver_options);

      $this->_connected = true;
    }
  }
?>
