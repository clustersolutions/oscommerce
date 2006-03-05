<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application.php');

  $page_contents = 'install_2.php';

  if (isset($_GET['step']) && is_numeric($_GET['step'])) {
    switch ($_GET['step']) {
      case '3':
        $page_contents = 'install_3.php';
        break;

      case '4':
        $page_contents = 'install_4.php';
        break;

      case '5':
        $page_contents = 'install_5.php';
        break;
    }
  }

  require('templates/main_page.php');
?>
