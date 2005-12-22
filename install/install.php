<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application.php');

  $page_contents = 'install.php';

  if (isset($_GET['step']) && isset($_POST['install']) && is_array($_POST['install'])) {
    switch ($_GET['step']) {
      case '2':
        $page_contents = 'install_2.php';
        break;

      case '3':
        if (in_array('configure', $_POST['install'])) {
          $page_contents = 'install_3.php';
        } else {
          $page_contents = 'install_5.php';
        }

        break;

      case '4':
        if (in_array('database', $_POST['install'])) {
          $page_contents = 'install_4.php';
        } else {
          $page_contents = 'install_5.php';
        }
        break;

      case '5':
        $page_contents = 'install_5.php';
        break;
    }
  }

  require('templates/main_page.php');
?>
