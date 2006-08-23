<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Boxes_shopping_cart extends osC_Modules {
    var $_title,
        $_code = 'shopping_cart',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_shopping_cart() {
      global $osC_Language;

      $this->_title = $osC_Language->get('box_shopping_cart_heading');
      $this->_title_link = osc_href_link(FILENAME_CHECKOUT, null, 'SSL');
    }

    function initialize() {
      global $osC_Language, $osC_ShoppingCart, $osC_Currencies;

      if ($osC_ShoppingCart->hasContents()) {
        $data = '<table border="0" width="100%" cellspacing="0" cellpadding="0">' . "\n";

        $products = $osC_ShoppingCart->getProducts();

        foreach ($osC_ShoppingCart->getProducts() as $products) {
          $data .= '  <tr>' . "\n" .
                   '    <td align="right" valign="top">';

          if (isset($_SESSION['new_products_id_in_cart']) && ($_SESSION['new_products_id_in_cart'] == $products['id'])) {
            $data .= '<span class="newItemInCart">';
          }

          $data .= $products['quantity'] . '&nbsp;x&nbsp;';

          if (isset($_SESSION['new_products_id_in_cart']) && ($_SESSION['new_products_id_in_cart'] == $products['id'])) {
            $data .= '</span>';
          }

          $data .= '</td>' . "\n" .
                   '    <td valign="top"><a href="' . osc_href_link(FILENAME_PRODUCTS, $products['keyword']) . '">';

          if (isset($_SESSION['new_products_id_in_cart']) && ($_SESSION['new_products_id_in_cart'] == $products['id'])) {
            $data .= '<span class="newItemInCart">';
          }

          $data .= $products['name'];

          if (isset($_SESSION['new_products_id_in_cart']) && ($_SESSION['new_products_id_in_cart'] == $products['id'])) {
            $data .= '</span>';
          }

          $data .= '</a></td>' . "\n" .
                   '  </tr>' . "\n";

          if (isset($_SESSION['new_products_id_in_cart']) && ($_SESSION['new_products_id_in_cart'] == $products['id'])) {
            unset($_SESSION['new_products_id_in_cart']);
          }
        }

        $data .= '</table>' . "\n" .
                 '<p style="text-align: right">Subtotal: ' . $osC_Currencies->format($osC_ShoppingCart->getSubTotal()) . '</p>' . "\n";
      } else {
        $data = $osC_Language->get('box_shopping_cart_empty');
      }

      $this->_content = $data;
    }
  }
?>
