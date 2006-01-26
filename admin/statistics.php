<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $module = (isset($_GET['module']) ? basename($_GET['module']) : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  if (!empty($module) && !file_exists('includes/modules/statistics/' . $module . '.php')) {
    $module = '';
  }

  if (empty($module)) {
    $page_contents = 'statistics_listing.php';
  } else {
    $page_contents = 'statistics.php';
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
