<?php
/*
  $Id:breadcrumb.php 293 2005-11-29 17:34:26Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_core {
    var $title = 'Core',
        $description = 'Load core classes.',
        $uninstallable = false,
        $depends = 'currencies',
        $precedes;

    function start() {
      global $osC_Customer, $osC_Tax, $osC_Weight, $osC_ShoppingCart, $osC_NavigationHistory;

      include('includes/classes/template.php');
      include('includes/classes/modules.php');
      include('includes/classes/category.php');
      include('includes/classes/product.php');
      include('includes/classes/datetime.php');
      include('includes/classes/xml.php');
      include('includes/classes/mime.php');
      include('includes/classes/email.php');

      include('includes/classes/customer.php');
      $osC_Customer = new osC_Customer();

      include('includes/classes/tax.php');
      $osC_Tax = new osC_Tax();

      include('includes/classes/weight.php');
      $osC_Weight = new osC_Weight();

      include('includes/classes/shopping_cart.php');
      $osC_ShoppingCart = new osC_ShoppingCart();

      include('includes/classes/navigation_history.php');
      $osC_NavigationHistory = new osC_NavigationHistory(true);

      return true;
    }

    function stop() {
      return true;
    }

    function install() {
      return false;
    }

    function remove() {
      return false;
    }

    function keys() {
      return false;
    }
  }
?>
