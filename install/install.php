<?php
/*
  $Id: install.php,v 1.5 2004/05/17 01:03:30 hpdl Exp $

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
        if (in_array('database', $_POST['install'])) {
          $page_contents = 'install_2.php';
        } elseif (in_array('configure', $_POST['install'])) {
          $page_contents = 'install_4.php';
        }
        break;
      case '3':
        if (in_array('database', $_POST['install'])) {
          $page_contents = 'install_3.php';
        }
        break;
      case '4':
        if (in_array('configure', $_POST['install'])) {
          $page_contents = 'install_4.php';
        }
        break;
      case '5':
        if (in_array('configure', $_POST['install'])) {
          if (isset($_POST['ENABLE_SSL']) && ($_POST['ENABLE_SSL'] == 'true')) {
            $page_contents = 'install_5.php';
          } else {
            if (in_array('database', $_POST['install'])) {
              $page_contents = 'install_7.php';
            } else {
              $page_contents = 'install_6.php';
            }
          }
        }
        break;
      case '6':
        if (in_array('configure', $_POST['install'])) {
            if (in_array('database', $_POST['install'])) {
            $page_contents = 'install_7.php';
          } else {
            $page_contents = 'install_6.php';
          }
        }
        break;
      case '7':
        if (in_array('configure', $_POST['install'])) {
          $page_contents = 'install_7.php';
        }
        break;
    }
  }

  require('templates/main_page.php');
?>
