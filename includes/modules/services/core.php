<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Services_core {
    public function start() {
      global $osC_Customer, $osC_Tax, $osC_Weight, $osC_ShoppingCart, $osC_NavigationHistory, $osC_Image;

      include('includes/classes/object_info.php');
      include('includes/classes/template.php');
      include('includes/classes/modules.php');
      include('includes/classes/category.php');
      include('includes/classes/variants.php');
      include('includes/classes/product.php');
      include('includes/classes/datetime.php');
      include('includes/classes/xml.php');
      include('includes/classes/mail.php');
      include('includes/classes/address.php');

      include('includes/classes/customer.php');
      $osC_Customer = new osC_Customer();

      include('includes/classes/tax.php');
      $osC_Tax = new osC_Tax();

      include('includes/classes/weight.php');
      $osC_Weight = new osC_Weight();

      include('includes/classes/shopping_cart.php');
      $osC_ShoppingCart = new osC_ShoppingCart();
      $osC_ShoppingCart->refresh();

      include('includes/classes/navigation_history.php');
      $osC_NavigationHistory = new osC_NavigationHistory(true);

      include('includes/classes/image.php');
      $osC_Image = new osC_Image();

      return true;
    }

    public function stop() {
      return true;
    }
  }
?>
