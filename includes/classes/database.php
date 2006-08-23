<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Database {
    var $is_connected = false,
        $link,
        $error_reporting = true,
        $error = false,
        $error_number,
        $error_query,
        $server,
        $username,
        $password,
        $debug = false,
        $number_of_queries = 0,
        $time_of_queries = 0;

    function &connect($server, $username, $password, $type = '') {
      if (empty($type)) {
        $type = DB_DATABASE_CLASS;
      }

      require('database/' . $type . '.php');

      $class = 'osC_Database_' . $type;
      $object = new $class($server, $username, $password);

      return $object;
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

    function &query($query) {
      $osC_Database_Result =& new osC_Database_Result($this);
      $osC_Database_Result->setQuery($query);

      return $osC_Database_Result;
    }

    function setError($error, $error_number = '', $query = '') {
      global $messageStack;

      if ($this->error_reporting === true) {
        $this->error = $error;
        $this->error_number = $error_number;
        $this->error_query = $query;

        if (isset($messageStack)) {
          $messageStack->add('debug', $this->getError());
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
          $fd = fopen($sql_file, 'rb');
          $import_queries = fread($fd, filesize($sql_file));
          fclose($fd);
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

            if ((strtoupper($next) == 'DROP T') || (strtoupper($next) == 'CREATE') || (strtoupper($next) == 'INSERT')) {
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

    function hasCreatePermission($database) {
      $db_created = false;

      if (empty($database)) {
        $this->setError(ERROR_DB_NO_DATABASE_SELECTED);

        return false;
      }

      $this->setErrorReporting(false);

      if ($this->selectDatabase($database) === false) {
        $this->setErrorReporting(true);

        if ($this->simpleQuery('create database ' . $database)) {
          $db_created = true;
        }
      }

      $this->setErrorReporting(true);

      if ($this->isError() === false) {
        if ($this->selectDatabase($database)) {
          if ($this->simpleQuery('create table osCommerceTestTable1536f ( temp_id int )')) {
            if ($db_created === true) {
              $this->simpleQuery('drop database ' . $database);
            } else {
              $this->simpleQuery('drop table osCommerceTestTable1536f');
            }
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

  class osC_Database_Result {
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
        $batch_select_field;

    function osC_Database_Result(&$db_class) {
      $this->db_class =& $db_class;
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

    function bindValueMixed($place_holder, $value, $type = 'string') {
      static $sql_parse_string;

      switch ($type) {
        case 'int':
          $value = intval($value);
          break;
        case 'float':
          $value = floatval($value);
          break;
        case 'raw':
          break;
        case 'string':
        default:
          $sql_parse_string = $this->db_class->sql_parse_string;

          $value = trim($value);

          if ($this->db_class->sql_parse_string_with_connection_handler === true) {
            $value = "'" . $sql_parse_string($value, $this->db_class->link) . "'";
          } else {
            $value = "'" . $sql_parse_string($value) . "'";
          }
      }

      $this->bindReplace($place_holder, $value);
    }

    function bindReplace($place_holder, $value) {
      $pos = strpos($this->sql_query, $place_holder);

      if ($pos !== false) {
        $length = strlen($place_holder);
        $character_after_place_holder = substr($this->sql_query, $pos+$length, 1);

        if (($character_after_place_holder === false) || ereg('[ ,)"]', $character_after_place_holder)) {
          $this->sql_query = substr_replace($this->sql_query, $value, $pos, $length);
        }
      }
    }

    function bindValue($place_holder, $value) {
      $this->bindValueMixed($place_holder, $value, 'string');
    }

    function bindInt($place_holder, $value) {
      $this->bindValueMixed($place_holder, $value, 'int');
    }

    function bindFloat($place_holder, $value) {
      $this->bindValueMixed($place_holder, $value, 'float');
    }

    function bindRaw($place_holder, $value) {
      $this->bindValueMixed($place_holder, $value, 'raw');
    }

    function bindTable($place_holder, $value) {
      $this->bindValueMixed($place_holder, $value, 'raw');
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

    function freeResult() {
      global $osC_Cache;

      if ($this->cache_read === false) {
        if (eregi('^SELECT', $this->sql_query)) {
          $this->db_class->freeResult($this->query_handler);
        }

        if (isset($this->cache_key)) {
          $osC_Cache->write($this->cache_key, $this->cache_data);
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

    function execute() {
      global $osC_Cache;

      if (isset($this->cache_key)) {
        if ($osC_Cache->read($this->cache_key, $this->cache_expire)) {
          $this->cache_data = $osC_Cache->cached_data;

          $this->cache_read = true;
        }
      }

      if ($this->cache_read === false) {
        $this->query_handler = $this->db_class->simpleQuery($this->sql_query, $this->debug);

        if ($this->batch_query === true) {
          $this->getBatchSize();

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
      return $this->query_handler = $this->db_class->randomQuery($this->sql_query);
    }

    function executeRandomMulti() {
      return $this->query_handler = $this->db_class->randomQueryMulti($this->sql_query);
    }

    function setCache($key, $expire = 0) {
      $this->cache_key = $key;
      $this->cache_expire = $expire;
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
      global $osC_Database;

      if (!isset($this->batch_size)) {
        $this->batch_size = $this->db_class->getBatchSize($this->sql_query, $this->batch_select_field);
      }

      return $this->batch_size;
    }

    function displayBatchLinksTotal($text) {
      return sprintf($text, $this->batch_from, $this->batch_to, $this->batch_size);
    }

    function displayBatchLinksPullDown($batch_keyword = 'page', $parameters = '') {
      global $osC_Language;

      $number_of_pages = ceil($this->batch_size / $this->batch_rows);

      if ($number_of_pages > 1) {
        $pages_array = array();
        for ($i=1; $i<=$number_of_pages; $i++) {
          $pages_array[] = array('id' => $i, 'text' => $i);
        }

        $get_parameter = '';
        $hidden_parameter = '';
        if (!empty($parameters)) {
          $parameters = explode('&', $parameters);
          foreach ($parameters as $parameter) {
            list($key, $value) = explode('=', $parameter);

            if ($key != $batch_keyword) {
              $get_parameter .= $key . '=' . $value . '&';
              $hidden_parameter .= osc_draw_hidden_field($key, $value);
            }
          }
        }

        $display_links = '<form action="' . osc_href_link(basename($_SERVER['PHP_SELF'])) . '" action="get">';

        if ($this->batch_number > 1) {
          $display_links .= osc_link_object(osc_href_link(basename($_SERVER['PHP_SELF']), $get_parameter . $batch_keyword . '=' . ($this->batch_number - 1)), $osC_Language->get('result_set_previous_page'), 'class="splitPageLink"');
        } else {
          $display_links .= $osC_Language->get('result_set_previous_page');
        }

        $display_links .= '&nbsp;&nbsp;' . sprintf($osC_Language->get('result_set_current_page'), osc_draw_pull_down_menu($batch_keyword, $pages_array, $this->batch_number, 'onchange="this.form.submit();"'), $number_of_pages) . '&nbsp;&nbsp;';

        if (($this->batch_number < $number_of_pages) && ($number_of_pages != 1)) {
          $display_links .= osc_link_object(osc_href_link(basename($_SERVER['PHP_SELF']), $get_parameter . $batch_keyword . '=' . ($this->batch_number + 1)), $osC_Language->get('result_set_next_page'), 'class="splitPageLink"');
        } else {
          $display_links .= $osC_Language->get('result_set_previous_page');
        }

        $display_links .= $hidden_parameter . osc_draw_hidden_session_id_field() . '</form>';
      } else {
        $display_links = sprintf($osC_Language->get('result_set_current_page'), 1, 1);
      }

      return $display_links;
    }

    function isBatchQuery() {
      if ($this->batch_query === true) {
        return true;
      }

      return false;
    }
  }
?>
