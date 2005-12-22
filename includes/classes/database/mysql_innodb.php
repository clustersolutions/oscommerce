<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('mysql.php');

  class osC_Database_mysql_innodb extends osC_Database_mysql {
    var $use_transactions = true,
        $use_fulltext = false,
        $use_fulltext_boolean = false;

    function osC_Database_mysql_innodb($server, $username, $password) {
      $this->osC_Database_mysql($server, $username, $password);
    }

    function prepareSearch($columns) {
      $search_sql = '(';

      foreach ($columns as $column) {
        $search_sql .= $column . ' like :keyword or ';
      }

      $search_sql = substr($search_sql, 0, -4) . ')';

      return $search_sql;
    }
  }
?>
