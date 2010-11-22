<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core;

  class Database {
    var $is_connected = false,
        $driver,
        $link,
        $error_reporting = true,
        $error = false,
        $error_number,
        $error_query,
        $server,
        $username,
        $password,
        $database,
        $port,
        $debug = false,
        $number_of_queries = 0,
        $time_of_queries = 0,
        $nextID = null,
        $logging_transaction = false,
        $logging_transaction_action = false,
        $fkeys = array();

    public static function initialize($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $port = DB_SERVER_PORT, $driver = DB_DATABASE_CLASS) {
      $class = 'osCommerce\\OM\\Core\\Database\\' . $driver;
      $object = new $class($server, $username, $password, $database, $port);

      $object->driver = $driver;

      return $object;
    }

    function getDriver() {
      return $this->driver;
    }

    function setConnected($boolean) {
      if ($boolean === true) {
        $this->is_connected = true;
      } else {
        $this->is_connected = false;
      }
    }

    function isConnected() {
      if ($this->is_connected === true) {
        return true;
      } else {
        return false;
      }
    }

    function query($query) {
      $osC_Database_Result = new DatabaseResult($this);
      $osC_Database_Result->setQuery($query);

      return $osC_Database_Result;
    }

    function setError($error, $error_number = '', $query = '') {
      if ($this->error_reporting === true) {
        $this->error = $error;
        $this->error_number = $error_number;
        $this->error_query = $query;

        error_log('[MYSQL] ' . $this->error . ' (' . $this->error_number . '): [QUERY] ' . $this->error_query);

        if ( Registry::exists('MessageStack') ) {
          Registry::get('MessageStack')->add('debug', $this->getError());
        }
      }
    }

    function isError() {
      if ($this->error === false) {
        return false;
      } else {
        return true;
      }
    }

    function getError() {
      if ($this->isError()) {
        $error = '';

        if (!empty($this->error_number)) {
          $error .= $this->error_number . ': ';
        }

        $error .= $this->error;

        if (!empty($this->error_query)) {
          $error .= '; ' . htmlentities($this->error_query);
        }

        return $error;
      } else {
        return false;
      }
    }

    function setErrorReporting($boolean) {
      if ($boolean === true) {
        $this->error_reporting = true;
      } else {
        $this->error_reporting = false;
      }
    }

    function setDebug($boolean) {
      if ($boolean === true) {
        $this->debug = true;
      } else {
        $this->debug = false;
      }
    }

    function importSQL($sql_file, $database, $table_prefix = -1) {
      if ($this->selectDatabase($database)) {
        if (file_exists($sql_file)) {
          $import_queries = file_get_contents($sql_file);
        } else {
          $this->setError(sprintf(ERROR_SQL_FILE_NONEXISTENT, $sql_file));

          return false;
        }

        if (!get_cfg_var('safe_mode')) {
          @set_time_limit(0);
        }

        $sql_queries = array();
        $sql_length = strlen($import_queries);
        $pos = strpos($import_queries, ';');

        for ($i=$pos; $i<$sql_length; $i++) {
// remove comments
          if ($import_queries[0] == '#') {
            $import_queries = ltrim(substr($import_queries, strpos($import_queries, "\n")));
            $sql_length = strlen($import_queries);
            $i = strpos($import_queries, ';')-1;
            continue;
          }

          if ($import_queries[($i+1)] == "\n") {
            $next = '';

            for ($j=($i+2); $j<$sql_length; $j++) {
              if (!empty($import_queries[$j])) {
                $next = substr($import_queries, $j, 6);

                if ($next[0] == '#') {
// find out where the break position is so we can remove this line (#comment line)
                  for ($k=$j; $k<$sql_length; $k++) {
                    if ($import_queries[$k] == "\n") {
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

            if (empty($next)) { // get the last insert query
              $next = 'insert';
            }

            if ((strtoupper($next) == 'DROP T') || (strtoupper($next) == 'CREATE') || (strtoupper($next) == 'INSERT') || (strtoupper($next) == 'ALTER ') || (strtoupper($next) == 'SET FO')) {
              $next = '';

              $sql_query = substr($import_queries, 0, $i);

              if ($table_prefix !== -1) {
                if (strtoupper(substr($sql_query, 0, 25)) == 'DROP TABLE IF EXISTS OSC_') {
                  $sql_query = 'DROP TABLE IF EXISTS ' . $table_prefix . substr($sql_query, 25);
                } elseif (strtoupper(substr($sql_query, 0, 17)) == 'CREATE TABLE OSC_') {
                  $sql_query = 'CREATE TABLE ' . $table_prefix . substr($sql_query, 17);
                } elseif (strtoupper(substr($sql_query, 0, 16)) == 'INSERT INTO OSC_') {
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

        for ($i=0, $n=sizeof($sql_queries); $i<$n; $i++) {
          $this->simpleQuery($sql_queries[$i]);

          if ($this->isError()) {
            break;
          }
        }
      }

      if ($this->isError()) {
        return false;
      } else {
        return true;
      }
    }

    function numberOfQueries() {
      return $this->number_of_queries;
    }

    function timeOfQueries() {
      return $this->time_of_queries;
    }

    function getMicroTime() {
      list($usec, $sec) = explode(' ', microtime());

      return ((float)$usec + (float)$sec);
    }
  }

  class DatabaseResult {
    var $db_class,
        $sql_query,
        $query_handler,
        $result,
        $rows,
        $affected_rows,
        $cache_key,
        $cache_expire,
        $cache_data,
        $cache_read = false,
        $debug = false,
        $batch_query = false,
        $batch_number,
        $batch_rows,
        $batch_size,
        $batch_to,
        $batch_from,
        $batch_select_field,
        $logging = false,
        $logging_module,
        $logging_module_id,
        $logging_fields = array(),
        $logging_changed = array(),
        $_db_tables = array();

    function __construct($db_class) {
      $this->db_class = $db_class;
    }

    function setQuery($query) {
      $this->sql_query = $query;
    }

    function appendQuery($query) {
      $this->sql_query .= ' ' . $query;
    }

    function getQuery() {
      return $this->sql_query;
    }

    function setDebug($boolean) {
      if ($boolean === true) {
        $this->debug = true;
      } else {
        $this->debug = false;
      }
    }

    function valueMixed($column, $type = 'string') {
      if (!isset($this->result)) {
        $this->next();
      }

      switch ($type) {
        case 'protected':
          return osc_output_string_protected($this->result[$column]);
          break;
        case 'int':
          return (int)$this->result[$column];
          break;
        case 'decimal':
          return (float)$this->result[$column];
          break;
        case 'string':
        default:
          return $this->result[$column];
      }
    }

    function value($column) {
      return $this->valueMixed($column, 'string');
    }

    function valueProtected($column) {
      return $this->valueMixed($column, 'protected');
    }

    function valueInt($column) {
      return $this->valueMixed($column, 'int');
    }

    function valueDecimal($column) {
      return $this->valueMixed($column, 'decimal');
    }

    function bindValueMixed($place_holder, $value, $type = 'string', $log = true) {
      if ($log === true) {
        $this->logging_fields[substr($place_holder, 1)] = $value;
      }

      switch ($type) {
        case 'int':
          $value = intval($value);
          break;
        case 'float':
          $value = floatval($value);
          break;
        case 'raw':
          break;
        case 'date':
          if ( empty($value) ) {
            $value = 'null';
          } else {
            $value = "'" . $this->db_class->parseString(trim($value)) . "'";
          }
          break;
        case 'string':
        default:
          $value = "'" . $this->db_class->parseString(trim($value)) . "'";
      }

      $this->bindReplace($place_holder, $value);
    }

    function bindReplace($place_holder, $value) {
      $this->sql_query = preg_replace('/\:\b' . substr($place_holder, 1) . '\b/', $value, $this->sql_query, 1);
    }

    function bindValue($place_holder, $value, $log = true) {
      $this->bindValueMixed($place_holder, $value, 'string', $log);
    }

    function bindInt($place_holder, $value, $log = true) {
      $this->bindValueMixed($place_holder, $value, 'int', $log);
    }

    function bindFloat($place_holder, $value, $log = true) {
      $this->bindValueMixed($place_holder, $value, 'float', $log);
    }

    function bindRaw($place_holder, $value, $log = true) {
      $this->bindValueMixed($place_holder, $value, 'raw', $log);
    }

    function bindDate($place_holder, $value, $log = true) {
      $this->bindValueMixed($place_holder, $value, 'date', $log);
    }

    function bindTable($place_holder, $value) {
      $this->bindValueMixed($place_holder, $value, 'raw', false);
    }

    function next() {
      if ($this->cache_read === true) {
        list(, $this->result) = each($this->cache_data);
      } else {
        if (!isset($this->query_handler)) {
          $this->execute();
        }

        $this->result = $this->db_class->next($this->query_handler);

        if (isset($this->cache_key)) {
          $this->cache_data[] = $this->result;
        }
      }

      return $this->result;
    }

    function getAll() {
      if ($this->cache_read === true) { // HPDL test cached results
        $this->result = $this->cache_data;
      } else {
        if (!isset($this->query_handler)) {
          $this->execute();
        }

        $this->result = $this->db_class->getAll($this->query_handler);

        if ( is_null($this->result) ) {
          $this->result = array();
        }

        if (isset($this->cache_key)) {
          $this->cache_data = $this->result;
        }
      }

      return $this->result;
    }

    function freeResult() {
      if ($this->cache_read === false) {
        if (preg_match('/^SELECT/i', $this->sql_query)) {
          $this->db_class->freeResult($this->query_handler);
        }

        if (isset($this->cache_key)) {
          Registry::get('Cache')->write($this->cache_data, $this->cache_key);
        }
      }

      unset($this);
    }

    function numberOfRows() {
      if (!isset($this->rows)) {
        if (!isset($this->query_handler)) {
          $this->execute();
        }

        if (isset($this->cache_key) && ($this->cache_read === true)) {
          $this->rows = sizeof($this->cache_data);
        } else {
          $this->rows = $this->db_class->numberOfRows($this->query_handler);
        }
      }

      return $this->rows;
    }

    function affectedRows() {
      if (!isset($this->affected_rows)) {
        if (!isset($this->query_handler)) {
          $this->execute();
        }

        $this->affected_rows = $this->db_class->affectedRows();
      }

      return $this->affected_rows;
    }

    function nextResultSet() {
      $this->db_class->nextResultSet($this->query_handler);
    }

    function execute() {
      if (isset($this->cache_key)) {
        if ( Registry::get('Cache')->read($this->cache_key, $this->cache_expire)) {
          $this->cache_data = Registry::get('Cache')->getCache();

          $this->cache_read = true;
        }
      }

      if ($this->cache_read === false) {
// Automatically bind table names
        $this->sql_query = str_replace(':table_', DB_TABLE_PREFIX, $this->sql_query);

        if ($this->db_class->use_foreign_keys == false) {
          $query_action = substr($this->sql_query, 0, strpos($this->sql_query, ' '));

          if ( ($query_action == 'delete') || ($query_action == 'update') ) {
            if ( empty($this->db_class->fkeys) ) {
              $Qfk = new self($this->db_class);
              $Qfk->setQuery('select * from :table_fk_relationships');
//              $Qfk->setCache('fk_relationships');
              $Qfk->execute();

              while ( $Qfk->next() ) {
                $this->db_class->fkeys[$Qfk->value('to_table')][] = array('from_table' => $Qfk->value('from_table'),
                                                                          'from_field' => $Qfk->value('from_field'),
                                                                          'to_field' => $Qfk->value('to_field'),
                                                                          'on_update' => $Qfk->value('on_update'),
                                                                          'on_delete' => $Qfk->value('on_delete'));
              }

              $Qfk->freeResult();
            }
          }

          if ($query_action == 'delete') {
            $query_data = explode(' ', $this->sql_query, 4);
            $query_table = substr($query_data[2], strlen(DB_TABLE_PREFIX));

            if ( isset($this->db_class->fkeys[$query_table]) ) {
// check for RESTRICT constraints first
              foreach ( $this->db_class->fkeys[$query_table] as $fk ) {
                if ( $fk['on_delete'] == 'restrict' ) {
                  $child_query = $this->db_class->simpleQuery('select ' . $fk['to_field'] . ' from ' . $query_data[2] . ' ' . $query_data[3]);
                  while ( $child_result = $this->db_class->next($child_query) ) {
                    $Qcheck = new self($this->db_class);
                    $Qcheck->setQuery('select ' . $fk['from_field'] . ' from ' . DB_TABLE_PREFIX .  $fk['from_table'] . ' where ' . $fk['from_field'] . ' = "' . $child_result[$fk['to_field']] . '" limit 1');
                    $Qcheck->execute();

                    if ( $Qcheck->numberOfRows() === 1 ) {
                      $this->db_class->setError('RESTRICT constraint condition from table ' . DB_TABLE_PREFIX .  $fk['from_table'], null, $this->sql_query);

                      return false;
                    }
                  }
                }
              }

              foreach ( $this->db_class->fkeys[$query_table] as $fk ) {
                $parent_query = $this->db_class->simpleQuery('select * from ' . $query_data[2] . ' ' . $query_data[3]);
                while ( $parent_result = $this->db_class->next($parent_query) ) {
                  if ( $fk['on_delete'] == 'cascade' ) {
                    $Qdel = new self($this->db_class);
                    $Qdel->setQuery('delete from :from_table where :from_field = :' . $fk['from_field']);
                    $Qdel->bindTable(':from_table', DB_TABLE_PREFIX . $fk['from_table']);
                    $Qdel->bindRaw(':from_field', $fk['from_field'], false);
                    $Qdel->bindValue(':' . $fk['from_field'], $parent_result[$fk['to_field']]);

                    if ( $this->logging === true ) {
                      if ( $this->db_class->logging_transaction === false ) {
                        $this->db_class->logging_transaction = true;
                      }

                      $Qdel->setLogging($this->logging_module, $this->logging_module_id);
                    }

                    $Qdel->execute();
                  } elseif ( $fk['on_delete'] == 'set_null' ) {
                    $Qupdate = new self($this->db_class);
                    $Qupdate->setQuery('update :from_table set :from_field = :' . $fk['from_field'] . ' where :from_field = :' . $fk['from_field']);
                    $Qupdate->bindTable(':from_table', DB_TABLE_PREFIX . $fk['from_table']);
                    $Qupdate->bindRaw(':from_field', $fk['from_field'], false);
                    $Qupdate->bindRaw(':' . $fk['from_field'], 'null');
                    $Qupdate->bindRaw(':from_field', $fk['from_field'], false);
                    $Qupdate->bindValue(':' . $fk['from_field'], $parent_result[$fk['to_field']], false);

                    if ( $this->logging === true ) {
                      if ( $this->db_class->logging_transaction === false ) {
                        $this->db_class->logging_transaction = true;
                      }

                      $Qupdate->setLogging($this->logging_module, $this->logging_module_id);
                    }

                    $Qupdate->execute();
                  }
                }
              }
            }
          } elseif ($query_action == 'update') {
            $query_data = explode(' ', $this->sql_query, 3);
            $query_table = substr($query_data[1], strlen(DB_TABLE_PREFIX));

            if ( isset($this->db_class->fkeys[$query_table]) ) {
// check for RESTRICT constraints first
              foreach ( $this->db_class->fkeys[$query_table] as $fk ) {
                if ( $fk['on_update'] == 'restrict' ) {
                  $child_query = $this->db_class->simpleQuery('select ' . $fk['to_field'] . ' from ' . $query_data[2] . ' ' . $query_data[3]);
                  while ( $child_result = $this->db_class->next($child_query) ) {
                    $Qcheck = new self($this->db_class);
                    $Qcheck->setQuery('select ' . $fk['from_field'] . ' from ' . DB_TABLE_PREFIX .  $fk['from_table'] . ' where ' . $fk['from_field'] . ' = "' . $child_result[$fk['to_field']] . '" limit 1');
                    $Qcheck->execute();

                    if ( $Qcheck->numberOfRows() === 1 ) {
                      $this->db_class->setError('RESTRICT constraint condition from table ' . DB_TABLE_PREFIX .  $fk['from_table'], null, $this->sql_query);

                      return false;
                    }
                  }
                }
              }

              foreach ( $this->db_class->fkeys[$query_table] as $fk ) {
// check to see if foreign key column value is being changed
                if ( strpos(substr($this->sql_query, strpos($this->sql_query, ' set ')+4, strpos($this->sql_query, ' where ') - strpos($this->sql_query, ' set ') - 4), ' ' . $fk['to_field'] . ' ') !== false ) {
                  $parent_query = $this->db_class->simpleQuery('select * from ' . $query_data[1] . substr($this->sql_query, strrpos($this->sql_query, ' where ')));
                  while ( $parent_result = $this->db_class->next($parent_query) ) {
                    if ( ($fk['on_update'] == 'cascade') || ($fk['on_update'] == 'set_null') ) {
                      $on_update_value = '';

                      if ( $fk['on_update'] == 'cascade' ) {
                        $on_update_value = $this->logging_fields[$fk['to_field']];
                      }

                      $Qupdate = new self($this->db_class);
                      $Qupdate->setQuery('update :from_table set :from_field = :' . $fk['from_field'] . ' where :from_field = :' . $fk['from_field']);
                      $Qupdate->bindTable(':from_table', DB_TABLE_PREFIX . $fk['from_table']);
                      $Qupdate->bindRaw(':from_field', $fk['from_field'], false);

                      if ( empty($on_update_value) ) {
                        $Qupdate->bindRaw(':' . $fk['from_field'], 'null');
                      } else {
                        $Qupdate->bindValue(':' . $fk['from_field'], $on_update_value);
                      }

                      $Qupdate->bindRaw(':from_field', $fk['from_field'], false);
                      $Qupdate->bindValue(':' . $fk['from_field'], $parent_result[$fk['to_field']], false);

                      if ( $this->logging === true ) {
                        if ( $this->db_class->logging_transaction === false ) {
                          $this->db_class->logging_transaction = true;
                        }

                        $Qupdate->setLogging($this->logging_module, $this->logging_module_id);
                      }

                      $Qupdate->execute();
                    }
                  }
                }
              }
            }
          }
        }

        if ($this->logging === true) {
          $this->logging_action = substr($this->sql_query, 0, strpos($this->sql_query, ' '));

          if ($this->logging_action == 'update') {
            $db = explode(' ', $this->sql_query, 3);
            $this->logging_database = $db[1];

            $test = $this->db_class->simpleQuery('select ' . implode(', ', array_keys($this->logging_fields)) . ' from ' . $this->logging_database . substr($this->sql_query, strrpos($this->sql_query, ' where ')));

            while ($result = $this->db_class->next($test)) {
              foreach ($this->logging_fields as $key => $value) {
                if ($result[$key] != $value) {
                  $this->logging_changed[] = array('key' => $this->logging_database . '.' . $key, 'old' => $result[$key], 'new' => $value);
                }
              }
            }
          } elseif ($this->logging_action == 'insert') {
            $db = explode(' ', $this->sql_query, 4);
            $this->logging_database = $db[2];

            foreach ($this->logging_fields as $key => $value) {
              $this->logging_changed[] = array('key' => $this->logging_database . '.' . $key, 'old' => '', 'new' => $value);
            }
          } elseif ($this->logging_action == 'delete') {
            $db = explode(' ', $this->sql_query, 4);
            $this->logging_database = $db[2];

            $del = $this->db_class->simpleQuery('select * from ' . $this->logging_database . ' ' . $db[3]);
            while ($result = $this->db_class->next($del)) {
              foreach ($result as $key => $value) {
                $this->logging_changed[] = array('key' => $this->logging_database . '.' . $key, 'old' => $value, 'new' => '');
              }
            }
          }
        }

        $this->query_handler = $this->db_class->simpleQuery($this->sql_query, $this->debug);

        if ($this->logging === true) {
          if ($this->db_class->logging_transaction_action === false) {
            $this->db_class->logging_transaction_action = $this->logging_action;
          }

          if ($this->affectedRows($this->query_handler) > 0) {
            if (!empty($this->logging_changed)) {
              if ( ($this->logging_action == 'insert') && !is_numeric($this->logging_module_id) ) {
                $this->logging_module_id = $this->db_class->nextID();
                $this->setNextID($this->logging_module_id);
              }

              if ( class_exists('osC_AdministratorsLog_Admin') ) {
                osC_AdministratorsLog_Admin::insert($this->logging_module, $this->db_class->logging_transaction_action, $this->logging_module_id, $this->logging_action, $this->logging_changed, $this->db_class->logging_transaction);
              }
            }
          }
        }

        if ($this->batch_query === true) {
          $this->batch_size = $this->db_class->getBatchSize($this->sql_query, $this->batch_select_field);

          $this->batch_to = ($this->batch_rows * $this->batch_number);
          if ($this->batch_to > $this->batch_size) {
            $this->batch_to = $this->batch_size;
          }

          $this->batch_from = ($this->batch_rows * ($this->batch_number - 1));
          if ($this->batch_to == 0) {
            $this->batch_from = 0;
          } else {
            $this->batch_from++;
          }
        }

        return $this->query_handler;
      }
    }

    function executeRandom() {
// Automatically bind table names
      $this->sql_query = str_replace(':table_', DB_TABLE_PREFIX, $this->sql_query);

      $this->query_handler = $this->db_class->randomQuery($this->sql_query);

      return $this->query_handler;
    }

    function executeRandomMulti() {
// Automatically bind table names
      $this->sql_query = str_replace(':table_', DB_TABLE_PREFIX, $this->sql_query);

      $this->query_handler = $this->db_class->randomQueryMulti($this->sql_query);

      return $this->query_handler;
    }

    function setCache($key, $expire = 0) {
      $this->cache_key = $key;
      $this->cache_expire = $expire;
    }

    function setLogging($module = null, $id = null) {
      $this->logging = true;

      if ( empty($module) ) {
        $module = OSCOM::getSiteApplication();
      }

      $this->logging_module = $module;
      $this->logging_module_id = $id;
    }

    function setNextID($id) {
      $this->db_class->nextID = $id;
    }

    function toArray() {
      if (!isset($this->result)) {
        $this->next();
      }

      return $this->result;
    }

    function prepareSearch($keywords, $columns, $embedded = false) {
      if ($embedded === true) {
        $this->sql_query .= ' and ';
      }

      $keywords_array = explode(' ', $keywords);

      if ($this->db_class->use_fulltext === true) {
        if ($this->db_class->use_fulltext_boolean === true) {
          $keywords = '';

          foreach ($keywords_array as $keyword) {
            if ((substr($keyword, 0, 1) != '-') && (substr($keyword, 0, 1) != '+')) {
              $keywords .= '+';
            }

            $keywords .= $keyword . ' ';
          }

          $keywords = substr($keywords, 0, -1);
        }

        $this->sql_query .= $this->db_class->prepareSearch($columns);
        $this->bindValue(':keywords', $keywords);
      } else {
        foreach ($keywords_array as $keyword) {
          $this->sql_query .= $this->db_class->prepareSearch($columns);

          foreach ($columns as $column) {
            $this->bindValue(':keyword', '%' . $keyword . '%');
          }

          $this->sql_query .= ' and ';
        }

        $this->sql_query = substr($this->sql_query, 0, -5);
      }
    }

    function setBatchLimit($batch_number = 1, $maximum_rows = 20, $select_field = '') {
      $this->batch_query = true;
      $this->batch_number = (is_numeric($batch_number) ? $batch_number : 1);
      $this->batch_rows = $maximum_rows;
      $this->batch_select_field = (empty($select_field) ? '*' : $select_field);

      $from = max(($this->batch_number * $maximum_rows) - $maximum_rows, 0);

      $this->sql_query = $this->db_class->setBatchLimit($this->sql_query, $from, $maximum_rows);

    }

    function getBatchSize() {
      return $this->batch_size;
    }

    function isBatchQuery() {
      if ($this->batch_query === true) {
        return true;
      }

      return false;
    }

    function getBatchTotalPages($text) {
      return sprintf($text, $this->batch_from, $this->batch_to, $this->batch_size);
    }

    function getBatchPageLinks($batch_keyword = 'page', $parameters = '', $with_pull_down_menu = true) {
      $string = $this->getBatchPreviousPageLink($batch_keyword, $parameters);

      if ( $with_pull_down_menu === true ) {
        $string .= $this->getBatchPagesPullDownMenu($batch_keyword, $parameters);
      }

      $string .= $this->getBatchNextPageLink($batch_keyword, $parameters);

      return $string;
    }

    function getBatchPagesPullDownMenu($batch_keyword = 'page', $parameters = '') {
      $number_of_pages = ceil($this->batch_size / $this->batch_rows);

      if ( $number_of_pages > 1 ) {
        $pages_array = array();

        for ( $i = 1; $i <= $number_of_pages; $i++ ) {
          $pages_array[] = array('id' => $i,
                                 'text' => $i);
        }

        $hidden_parameter = '';

        if ( !empty($parameters) ) {
          $parameters = explode('&', $parameters);

          foreach ( $parameters as $parameter ) {
            $keys = explode('=', $parameter, 2);

            if ( $keys[0] != $batch_keyword ) {
              $hidden_parameter .= osc_draw_hidden_field($keys[0], (isset($keys[1]) ? $keys[1] : ''));
            }
          }
        }

        $string = '<form action="' . OSCOM::getLink(null, null) . '" action="get">' . $hidden_parameter .
                  sprintf(OSCOM::getDef('result_set_current_page'), osc_draw_pull_down_menu($batch_keyword, $pages_array, $this->batch_number, 'onchange="this.form.submit();"'), $number_of_pages) .
                  osc_draw_hidden_session_id_field() . '</form>';
      } else {
        $string = sprintf(OSCOM::getDef('result_set_current_page'), 1, 1);
      }

      return $string;
    }

    function getBatchPreviousPageLink($batch_keyword = 'page', $parameters = '') {
      $get_parameter = '';

      if ( !empty($parameters) ) {
        $parameters = explode('&', $parameters);

        foreach ( $parameters as $parameter ) {
          $keys = explode('=', $parameter, 2);

          if ( $keys[0] != $batch_keyword ) {
            $get_parameter .= $keys[0] . (isset($keys[1]) ? '=' . $keys[1] : '') . '&';
          }
        }
      }

      if ( defined('OSC_IN_ADMIN') && ( OSC_IN_ADMIN === true ) ) {
        $back_string = osc_icon('nav_back.png');
        $back_grey_string = osc_icon('nav_back_grey.png');
      } else {
        $back_string = OSCOM::getDef('result_set_previous_page');
        $back_grey_string = OSCOM::getDef('result_set_previous_page');
      }

      if ( $this->batch_number > 1 ) {
        $string = osc_link_object(OSCOM::getLink(null, null, $get_parameter . $batch_keyword . '=' . ($this->batch_number - 1)), $back_string);
      } else {
        $string = $back_grey_string;
      }

      $string .= '&nbsp;';

      return $string;
    }

    function getBatchNextPageLink($batch_keyword = 'page', $parameters = '') {
      $number_of_pages = ceil($this->batch_size / $this->batch_rows);

      $get_parameter = '';

      if ( !empty($parameters) ) {
        $parameters = explode('&', $parameters);

        foreach ( $parameters as $parameter ) {
          $keys = explode('=', $parameter, 2);

          if ( $keys[0] != $batch_keyword ) {
            $get_parameter .= $keys[0] . (isset($keys[1]) ? '=' . $keys[1] : '') . '&';
          }
        }
      }

      if ( defined('OSC_IN_ADMIN') && ( OSC_IN_ADMIN === true ) ) {
        $forward_string = osc_icon('nav_forward.png');
        $forward_grey_string = osc_icon('nav_forward_grey.png');
      } else {
        $forward_string = OSCOM::getDef('result_set_next_page');
        $forward_grey_string = OSCOM::getDef('result_set_next_page');
      }

      $string = '&nbsp;';

      if ( ( $this->batch_number < $number_of_pages ) && ( $number_of_pages != 1 ) ) {
        $string .= osc_link_object(OSCOM::getLink(null, null, $get_parameter . $batch_keyword . '=' . ($this->batch_number + 1)), $forward_string);
      } else {
        $string .= $forward_grey_string;
      }

      return $string;
    }
  }
?>
