<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Database\Microsoft;

  class SqlServer extends \osCommerce\OM\Core\Database {
    public function __construct($server = '.\SQLEXPRESS', $username = 'sa', $password = 'password1', $database = 'osc3', $port = null) {
      $this->server = $server;
      $this->username = $username;
      $this->password = $password;
      $this->database = $database;
      $this->port = $port;

      if ($this->is_connected === false) {
        $this->connect();
      }
    }

    public function connect() {
      if ( empty($this->port) ) {
        $this->port = null;
      }

      if ( $this->_connect() ) {
        $this->setConnected(true);

        return true;
      } else {
//        $this->setError($this->_connect_error(), $this->_connect_errno());

        return false;
      }
    }

    protected function _connect() {
      $this->link = sqlsrv_connect($this->server, array('UID' => $this->username,
                                                        'PWD' => $this->password,
                                                        'Database' => $this->database));

      return ( $this->link !== false );
    }

    public function parseString($value) {
      return addslashes($value);
    }

    public function simpleQuery($query, $debug = false) {
      global $osC_MessageStack, $osC_Services;

      if ($this->isConnected()) {
        $this->number_of_queries++;

        $resource = sqlsrv_query($this->link, $query);

        if ($resource !== false) {
          $this->error = false;
          $this->error_number = null;
          $this->error_query = null;

          return $resource;
        } else {
//          $this->setError($this->_error(), $this->_errno(), $query);

          return false;
        }
      } else {
        return false;
      }
    }

    public function next($resource) {
      return sqlsrv_fetch_array($resource, SQLSRV_FETCH_ASSOC);
    }

    public function nextResultSet($resource) {
      return sqlsrv_next_result($resource);
    }

    public function freeResult($resource) {
      return sqlsrv_free_stmt($resource);
    }

    public function numberOfRows($resource) {
      return sqlsrv_num_rows($resource);
    }

    public function getAll($resource) {
      $result = array();

      while ( $data = $this->next($resource) ) {
        $result[] = $data;
      }

      return $result;
    }
  }
?>
