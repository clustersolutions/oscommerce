<?php
/*
  $Id: index.php 208 2005-09-26 23:08:58 +0200 (Mo, 26 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/languages/' . $_SESSION['language'] . '/' . FILENAME_PRODUCTS);

  $osC_Template = osC_Template::setup('products');

  require('templates/' . $osC_Template->getCode() . '.php');

  require('includes/application_bottom.php');
?>
