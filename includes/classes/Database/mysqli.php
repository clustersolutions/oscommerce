<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Database_mysqli extends OSCOM_Database {
    var $use_mysqli = false,
        $use_transactions = false,
        $use_foreign_keys = false,
        $use_fulltext = false,
        $use_fulltext_boolean = false;

    function __construct($server, $username, $password, $database, $port) {
      if ( function_exists('mysqli_connect') ) {
        $this->use_mysqli = true;
      }

      $this->server = $server;
      $this->username = $username;
      $this->password = $password;
      $this->database = $database;
      $this->port = $port;

      if ($this->is_connected === false) {
        $this->connect();
      }
    }

    function connect() {
      if ( empty($this->port) ) {
        $this->port = null;
      }

      if ( $this->_connect() ) {
        $this->setConnected(true);

        if ( version_compare($this->getServerVersion(), '5.0.2') >= 0 ) {
          $this->simpleQuery('set session sql_mode="STRICT_ALL_TABLES"');
        }

        return true;
      } else {
        $this->setError($this->_connect_error(), $this->_connect_errno());

        return false;
      }
    }

    function disconnect() {
      if ( $this->isConnected() ) {
        return $this->_close();
      }

      return true;
    }

    function selectDatabase($database) {
      if ( $this->isConnected() ) {
        if ( $this->_select_db($database) ) {
          $this->database = $database;

          return true;
        } else {
          $this->setError($this->_error(), $this->_errno());

          return false;
        }
      } else {
        return false;
      }
    }

    function getServerVersion() {
      return $this->_get_server_info();
    }

    function parseString($value) {
      return $this->_real_escape_string($value);
    }

    function simpleQuery($query, $debug = false) {
      global $osC_MessageStack, $osC_Services;

      if ($this->isConnected()) {
        $this->number_of_queries++;

        if ( ($debug === false) && ($this->debug === true) ) {
          $debug = true;
        }

        if (isset($osC_Services) && $osC_Services->isStarted('debug')) {
          if ( ($debug === false) && (SERVICE_DEBUG_OUTPUT_DB_QUERIES == '1') ) {
            $debug = true;
          }

          if (!osc_empty(SERVICE_DEBUG_EXECUTION_TIME_LOG) && (SERVICE_DEBUG_LOG_DB_QUERIES == '1')) {
            error_log('QUERY ' . $query . "\n", 3, SERVICE_DEBUG_EXECUTION_TIME_LOG);
          }
        } elseif ($debug === true) {
          $debug = false;
        }

        if ($debug === true) {
          $time_start = $this->getMicroTime();
        }

        $resource = $this->_query($query);

        if ($debug === true) {
          $time_end = $this->getMicroTime();

          $query_time = number_format($time_end - $time_start, 5);

          if ($this->debug === true) {
            $this->time_of_queries += $query_time;
          }

          echo '<div style="font-family: Verdana, Arial, sans-serif; font-size: 7px; font-weight: bold;">[<a href="#query' . $this->number_of_queries . '">#' . $this->number_of_queries . '</a>]</div>';

          $osC_MessageStack->add('debug', '<a name=\'query' . $this->number_of_queries . '\'></a>[#' . $this->number_of_queries . ' - ' . $query_time . 's] ' . $query, 'warning');
        }

        if ($resource !== false) {
          $this->error = false;
          $this->error_number = null;
          $this->error_query = null;

          if ( $this->use_mysqli === true ) {
            if ( mysqli_warning_count($this->link) > 0 ) {
              $warning_query = mysqli_query($this->link, 'show warnings');
              while ( $warning = mysqli_fetch_row($warning_query) ) {
                error_log(sprintf('[MYSQL] %s (%d): %s [QUERY] ' . $query, $warning[0], $warning[1], $warning[2]));
              }

              mysqli_free_result($warning_query);
            }
          }

          return $resource;
        } else {
          $this->setError($this->_error(), $this->_errno(), $query);

          return false;
        }
      } else {
        return false;
      }
    }

    function dataSeek($row_number, $resource = null) {
      return $this->_data_seek($row_number, $resource);
    }

    function randomQuery($query) {
      $query .= ' order by rand() limit 1';

      return $this->simpleQuery($query);
    }

    function randomQueryMulti($query) {
      $resource = $this->simpleQuery($query);

      $num_rows = $this->numberOfRows($resource);

      if ($num_rows > 0) {
        $random_row = osc_rand(0, ($num_rows - 1));

        $this->dataSeek($random_row, $resource);
      }

      return $resource;
    }

    function next($resource) {
      return $this->_fetch_assoc($resource);
    }

    function freeResult($resource) {
      return $this->_free_result($resource);
    }

    function nextID() {
      if ( is_numeric($this->nextID) ) {
        $id = $this->nextID;
        $this->nextID = null;

        return $id;
      } elseif ( $id = $this->_insert_id() ) {
        return $id;
      } else {
        $this->setError($this->_error(), $this->_errno());

        return false;
      }
    }

    function numberOfRows($resource) {
      return $this->_num_rows($resource);
    }

    function affectedRows() {
      return $this->_affected_rows();
    }

    function startTransaction() {
      $this->logging_transaction = true;

      if ($this->use_transactions === true) {
        return $this->_trans_start();
      }

      return false;
    }

    function commitTransaction() {
      if ($this->logging_transaction === true) {
        $this->logging_transaction = false;
        $this->logging_transaction_action = false;
      }

      if ($this->use_transactions === true) {
        return $this->_trans_commit();
      }

      return false;
    }

    function rollbackTransaction() {
      if ($this->logging_transaction === true) {
        $this->logging_transaction = false;
        $this->logging_transaction_action = false;
      }

      if ($this->use_transactions === true) {
        return $this->_trans_rollback();
      }

      return false;
    }

    function setBatchLimit($sql_query, $from, $maximum_rows) {
      return $sql_query . ' limit ' . $from . ', ' . $maximum_rows;
    }

    function getBatchSize($sql_query, $select_field = '*') {
      if (strpos($sql_query, 'SQL_CALC_FOUND_ROWS') !== false) {
        $bb = $this->query('select found_rows() as total');
      } else {
        $total_query = substr($sql_query, 0, strpos($sql_query, ' limit '));

        $pos_to = strlen($total_query);
        $pos_from = strpos($total_query, ' from ');

        if (($pos_group_by = strpos($total_query, ' group by ', $pos_from)) !== false) {
          if ($pos_group_by < $pos_to) {
            $pos_to = $pos_group_by;
          }
        }

        if (($pos_having = strpos($total_query, ' having ', $pos_from)) !== false) {
          if ($pos_having < $pos_to) {
            $pos_to = $pos_having;
          }
        }

        if (($pos_order_by = strpos($total_query, ' order by ', $pos_from)) !== false) {
          if ($pos_order_by < $pos_to) {
            $pos_to = $pos_order_by;
          }
        }

        $bb = $this->query('select count(' . $select_field . ') as total ' . substr($total_query, $pos_from, ($pos_to - $pos_from)));
      }

      return $bb->value('total');
    }

    function prepareSearch($columns) {
      if ($this->use_fulltext === true) {
        return 'match (' . implode(', ', $columns) . ') against (:keywords' . (($this->use_fulltext_boolean === true) ? ' in boolean mode' : '') . ')';
      } else {
        $search_sql = '(';

        foreach ($columns as $column) {
          $search_sql .= $column . ' like :keyword or ';
        }

        $search_sql = substr($search_sql, 0, -4) . ')';

        return $search_sql;
      }
    }

    function _connect() {
      $this->link = false;

      if ( $this->use_mysqli === true ) {
        $this->link = mysqli_connect((DB_SERVER_PERSISTENT_CONNECTIONS === true ? 'p:' : '') . $this->server, $this->username, $this->password, $this->database, $this->port);
      } else {
        if ( DB_SERVER_PERSISTENT_CONNECTIONS === true ) {
          $this->link = mysql_pconnect($this->server . ( !empty($this->port) ? ':' . $this->port : ''), $this->username, $this->password);
        } else {
          $this->link = mysql_connect($this->server . ( !empty($this->port) ? ':' . $this->port : ''), $this->username, $this->password);
        }

        if ( ($this->link !== false) && !empty($this->database) ) {
          mysql_select_db($this->database, $this->link);
        }
      }

      return ( $this->link !== false );
    }

    function _connect_error() {
      if ( $this->use_mysqli === true ) {
        return mysqli_connect_error();
      }

      return mysql_error();
    }

    function _connect_errno() {
      if ( $this->use_mysqli === true ) {
        return mysqli_connect_errno();
      }

      return mysql_errno();
    }

    function _close() {
      if ( $this->use_mysqli === true ) {
        return mysqli_close($this->link);
      }

      return mysql_close($this->link);
    }

    function _select_db($database) {
      if ( $this->use_mysqli === true ) {
        return mysqli_select_db($this->link, $database);
      }

      return mysql_select_db($database, $this->link);
    }

    function _error() {
      if ( $this->use_mysqli === true ) {
        return mysqli_error($this->link);
      }

      return mysql_error($this->link);
    }

    function _errno() {
      if ( $this->use_mysqli === true ) {
        return mysqli_errno($this->link);
      }

      return mysql_errno($this->link);
    }

    function _get_server_info() {
      if ( $this->use_mysqli === true ) {
        return mysqli_get_server_info($this->link);
      }

      return mysql_get_server_info($this->link);
    }

    function _real_escape_string($value) {
      if ( $this->use_mysqli === true ) {
        return mysqli_real_escape_string($this->link, $value);
      }

      return mysql_real_escape_string($value, $this->link);
    }

    function _query($query) {
      if ( $this->use_mysqli === true ) {
        return mysqli_query($this->link, $query);
      }

      return mysql_query($query, $this->link);
    }

    function _data_seek($row_number, $resource = null) {
      if ( empty($resource) ) {
        $resource =& $this->link;
      }

      if ( $this->use_mysqli === true ) {
        return mysqli_data_seek($resource, $row_number);
      }

      return mysql_data_seek($row_number, $resource);
    }

    function _fetch_assoc($resource) {
      if ( $this->use_mysqli === true ) {
        return mysqli_fetch_assoc($resource);
      }

      return mysql_fetch_assoc($resource);
    }

    function _free_result($resource) {
      if ( $this->use_mysqli === true ) {
        return mysqli_free_result($resource);
      }

      return mysql_free_result($resource);
    }

    function _insert_id() {
      if ( $this->use_mysqli === true ) {
        return mysqli_insert_id($this->link);
      }

      return mysql_insert_id($this->link);
    }

    function _num_rows($resource) {
      if ( $this->use_mysqli === true ) {
        return mysqli_num_rows($resource);
      }

      return mysql_num_rows($resource);
    }

    function _affected_rows() {
      if ( $this->use_mysqli === true ) {
        return mysqli_affected_rows($this->link);
      }

      return mysql_affected_rows($this->link);
    }

    function _trans_start() {
      if ( $this->use_mysqli === true ) {
        return mysqli_autocommit($this->link, false);
      }

      return $this->simpleQuery('start transaction');
    }

    function _trans_commit() {
      if ( $this->use_mysqli === true ) {
        $result = mysqli_commit($this->link);

        mysqli_autocommit($this->link, true);

        return $result;
      }

      return $this->simpleQuery('commit');
    }

    function _trans_rollback() {
      if ( $this->use_mysqli === true ) {
        $result = mysqli_rollback($this->link);

        mysqli_autocommit($this->link, true);

        return $result;
      }

      return $this->simpleQuery('rollback');
    }
  }
?>
