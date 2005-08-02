<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application.php');

  $page_contents = 'upgrade.php';

  if (isset($_GET['step'])) {
    switch ($_GET['step']) {
      case '2':
        $page_contents = 'upgrade_2.php';
        break;
      case '3':
        $page_contents = 'upgrade_3.php';
        break;
    }
  }

  require('templates/main_page.php');
?>
