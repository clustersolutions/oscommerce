<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Actions_cart_add {
    function execute() {
      global $osC_Session, $osC_ShoppingCart, $osC_Product;

      if (!isset($osC_Product)) {
        $id = false;

        foreach ($_GET as $key => $value) {
          if ( (ereg('^[0-9]+(#?([0-9]+:?[0-9]+)+(;?([0-9]+:?[0-9]+)+)*)*$', $key) || ereg('^[a-zA-Z0-9 -_]*$', $key)) && ($key != $osC_Session->getName()) ) {
            $id = $key;
          }

          break;
        }

        if (($id !== false) && osC_Product::checkEntry($id)) {
          $osC_Product = new osC_Product($id);
        }
      }

      if (isset($osC_Product)) {
        if (isset($_POST['attributes']) && is_array($_POST['attributes'])) {
          $osC_ShoppingCart->add($osC_Product->getID(), $_POST['attributes']);
        } else {
          if ($osC_Product->hasAttributes()) {
            osc_redirect(osc_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword()));

            return false;
          }

          $osC_ShoppingCart->add($osC_Product->getID());
        }
      }

      osc_redirect(osc_href_link(FILENAME_CHECKOUT));
    }
  }
?>
