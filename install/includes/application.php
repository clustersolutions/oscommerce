<?php
/*
  $Id: application.php,v 1.7 2004/05/17 01:03:31 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

// Set the level of error reporting
  error_reporting(E_ALL); // & ~E_NOTICE);

  require('../includes/functions/compatibility.php');

  if (isset($_GET['language'])) {
    setcookie('osC_Language', $_GET['language']);

    $language = $_GET['language'];
  } elseif (isset($_COOKIE['osC_Language'])) {
    $language = $_COOKIE['osC_Language'];
  } else {
    $language = 'english';
  }

  require('languages/' . $language . '.php');
  require('languages/' . $language . '/' . basename($_SERVER['PHP_SELF']));

  require('../includes/functions/general.php');
  require('functions/general.php');
  require('../includes/functions/html_output.php');

  require('../includes/classes/database.php');
?>
