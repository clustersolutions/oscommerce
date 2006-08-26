<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Actions_cart_update {
    function execute() {
      global $osC_ShoppingCart;

      if (isset($_POST['products']) && is_array($_POST['products']) && !empty($_POST['products'])) {
        foreach ($_POST['products'] as $product => $quantity) {
          if (!is_numeric($quantity)) {
            return false;
          }

          $product = explode('#', $product, 2);
          $attributes_array = array();

          if (isset($product[1])) {
            $attributes = explode(';', $product[1]);

            foreach ($attributes as $set) {
              $attribute = explode(':', $set);

              if (!is_numeric($attribute[0]) || !is_numeric($attribute[1])) {
                return false;
              }

              $attributes_array[$attribute[0]] = $attribute[1];
            }
          }

          $osC_ShoppingCart->add($product[0], $attributes_array, $quantity);
        }
      }

      osc_redirect(osc_href_link(FILENAME_CHECKOUT));
    }
  }
?>
