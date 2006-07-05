<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require('includes/classes/image.php');

  $module = (isset($_GET['module']) ? basename($_GET['module']) : '');

  if (!empty($module) && !file_exists('includes/modules/image/' . $module . '.php')) {
    $module = '';
  }

  if (empty($module)) {
    $page_contents = 'images_listing.php';
  } else {
    $page_contents = 'images.php';
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>