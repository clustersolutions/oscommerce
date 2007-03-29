<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Actions_cart_remove {
    function execute() {
      global $osC_Session, $osC_ShoppingCart;

      $id = false;

      foreach ($_GET as $key => $value) {
        if ( (ereg('^[0-9]+(#?([0-9]+:?[0-9]+)+(;?([0-9]+:?[0-9]+)+)*)*$', $key) || ereg('^[a-zA-Z0-9 -_]*$', $key)) && ($key != $osC_Session->getName()) ) {
          $id = $key;
        }

        break;
      }

      if (($id !== false) && osC_Product::checkEntry($id)) {
        $osC_Product = new osC_Product($id);

        $product_id = $osC_Product->getID();

        if (isset($_GET['attributes']) && ereg('^([0-9]+:?[0-9]+)+(;?([0-9]+:?[0-9]+)+)*$', $_GET['attributes'])) {
          $product_id .= '#' . $_GET['attributes'];
        }

        $osC_ShoppingCart->remove($product_id);
      }

      osc_redirect(osc_href_link(FILENAME_CHECKOUT));
    }
  }
?>
