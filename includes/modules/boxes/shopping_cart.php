<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Boxes_shopping_cart extends osC_Modules {
    var $_title = 'Shopping Cart',
        $_code = 'shopping_cart',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_shopping_cart() {
//      $this->_title = BOX_HEADING_SHOPPING_CART;
      $this->_title_link = tep_href_link(FILENAME_CHECKOUT, '', 'SSL');
    }

    function initialize() {
      global $osC_Currencies;

      if ($_SESSION['cart']->count_contents() > 0) {
        $data = '<table border="0" width="100%" cellspacing="0" cellpadding="0">' . "\n";

        $products = $_SESSION['cart']->get_products();

        foreach ($_SESSION['cart']->get_products() as $products) {
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
                   '    <td valign="top"><a href="' . tep_href_link(FILENAME_PRODUCTS, $products['keyword']) . '">';

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
                 '<p style="text-align: right">Subtotal: ' . $osC_Currencies->format($_SESSION['cart']->show_total()) . '</p>' . "\n";
      } else {
        $data = BOX_SHOPPING_CART_EMPTY;
      }

      $this->_content = $data;
    }
  }
?>
