<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Checkout_Callback extends osC_Template {

/* Private variables */

    var $_module = 'callback';

/* Class constructor */

    function osC_Checkout_Callback() {
      if (isset($_GET['module']) && (empty($_GET['module']) === false)) {
        if (file_exists('includes/modules/payment/' . $_GET['module'] . '.php')) {
          include('includes/classes/order.php');

          include('includes/classes/payment.php');
          include('includes/modules/payment/' . $_GET['module'] . '.php');

          $module = 'osC_Payment_' . $_GET['module'];
          $module = new $module();
          $module->callback();
        }
      }

      exit;
    }
  }
?>
