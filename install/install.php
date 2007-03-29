<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/application.php');

  $page_contents = 'install.php';

  if (isset($_GET['step']) && is_numeric($_GET['step'])) {
    switch ($_GET['step']) {
      case '2':
        $page_contents = 'install_2.php';
        break;

      case '3':
        $page_contents = 'install_3.php';
        break;

      case '4':
        $page_contents = 'install_4.php';
        break;
    }
  }

  require('templates/main_page.php');
?>
