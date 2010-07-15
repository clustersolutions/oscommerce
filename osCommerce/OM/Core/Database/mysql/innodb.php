<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Database\mysql;

  class innodb extends \osCommerce\OM\Core\Database\mysql {
    var $use_transactions = true,
        $use_foreign_keys = true,
        $use_fulltext = false,
        $use_fulltext_boolean = false;

    function __construct($server, $username, $password, $database, $port) {
      parent::__construct($server, $username, $password, $database, $port);
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
