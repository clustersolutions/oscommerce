<?php
/*
  $Id: statistics.php,v 1.2 2004/07/22 22:45:46 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $selected_box = 'reports';

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
