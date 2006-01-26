<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

// Set the level of error reporting
  error_reporting(E_ALL); // & ~E_NOTICE);

  define('DEFAULT_LANGUAGE', 'en_US');

  require('../includes/functions/compatibility.php');

  require('../includes/functions/general.php');
  require('functions/general.php');
  require('../includes/functions/html_output.php');

  require('../includes/classes/database.php');

  require('includes/classes/language.php');
  $osC_Language = new osC_LanguageInstall();
?>
