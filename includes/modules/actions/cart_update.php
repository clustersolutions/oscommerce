<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Actions_cart_update {
    function execute() {
      global $osC_ShoppingCart;

      if ( isset($_POST['products']) && is_array($_POST['products']) && !empty($_POST['products']) ) {
        foreach ( $_POST['products'] as $item_id => $quantity ) {
          if ( !is_numeric($item_id) || !is_numeric($quantity) ) {
            return false;
          }

          $osC_ShoppingCart->update($item_id, $quantity);
        }
      }

      osc_redirect(osc_href_link(FILENAME_CHECKOUT));
    }
  }
?>
