<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Actions {
    function parse() {
      if (isset($_GET['action']) && !empty($_GET['action'])) {
        $_GET['action'] = basename($_GET['action']);

        if (file_exists('includes/modules/actions/' . $_GET['action'] . '.php')) {
          include('includes/modules/actions/' . $_GET['action'] . '.php');

          call_user_func(array('osC_Actions_' . $_GET['action'], 'execute'));
        }
      }
    }
  }
?>
