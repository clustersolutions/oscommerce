<?php
/*
  $Id: mysql_innodb.php,v 1.2 2004/05/17 01:03:28 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('mysql.php');

  class osC_Database_mysql_innodb extends osC_Database_mysql {
    var $use_transactions = true;

    function osC_Database_mysql_innodb($server, $username, $password) {
      $this->osC_Database_mysql($server, $username, $password);
    }
  }
?>
