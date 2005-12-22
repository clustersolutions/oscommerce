<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

// Set the level of error reporting
  error_reporting(E_ALL); // & ~E_NOTICE);

  require('../includes/functions/compatibility.php');

  require('includes/classes/language.php');
  $osC_Language = new osC_Language();

  $language = $osC_Language->_languages[$osC_Language->language];

  require('../includes/functions/general.php');
  require('functions/general.php');
  require('../includes/functions/html_output.php');

  require('../includes/classes/database.php');
?>
