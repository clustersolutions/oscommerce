<?php
/*
  $Id:cart.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Checkout_Cart extends osC_Template {

/* Private variables */

    var $_module = 'cart',
        $_group = 'checkout',
        $_page_title = HEADING_TITLE_CHECKOUT_SHOPPING_CART,
        $_page_contents = 'shopping_cart.php',
        $_products_out_of_stock = false;

/* Class constructor */

    function osC_Checkout_Cart() {
      global $osC_Services, $breadcrumb;

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_CHECKOUT_SHOPPING_CART, tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
      }

      if ($_GET[$this->_module] == 'update') {
        $this->_process();
      }
    }

/* Public methods */

    function getListing() {
      return $_SESSION['cart']->get_products();
    }

    function getAttributes($id) {
      global $osC_Database, $osC_Session, $osC_Language;

      foreach ($_SESSION['cart']->get_products() as $product) {
        if ($product['id'] == $id) {
          if (isset($product['attributes']) && is_array($product['attributes'])) {
            $array = array();

            while (list($option, $value) = each($product['attributes'])) {
              $Qattributes = $osC_Database->query('select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from :table_products_options popt, :table_products_options_values poval, :table_products_attributes pa where pa.products_id = :products_id and pa.options_id = :options_id and pa.options_id = popt.products_options_id and pa.options_values_id = :options_values_id and pa.options_values_id = poval.products_options_values_id and popt.language_id = :language_id and poval.language_id = :language_id');
              $Qattributes->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
              $Qattributes->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
              $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
              $Qattributes->bindInt(':products_id', $product['id']);
              $Qattributes->bindInt(':options_id', $option);
              $Qattributes->bindInt(':options_values_id', $value);
              $Qattributes->bindInt(':language_id', $osC_Language->getID());
              $Qattributes->bindInt(':language_id', $osC_Language->getID());
              $Qattributes->execute();

              $array[] = array('options_id' => $option,
                               'options_values_id' => $value,
                               'products_options_name' => $Qattributes->value('products_options_name'),
                               'products_options_values_name' => $Qattributes->value('products_options_values_name'),
                               'options_values_price' => $Qattributes->value('options_values_price'),
                               'price_prefix' => $Qattributes->value('price_prefix'));
            }

            return $array;
          }
        }
      }
    }

    function hasAttributes($id) {
      foreach ($_SESSION['cart']->get_products() as $product) {
        if ($product['id'] == $id) {
          if (isset($product['attributes']) && is_array($product['attributes'])) {
            return true;
          } else {
            break;
          }
        }
      }

      return false;
    }

    function hasStock($id, $quantity) {
      global $osC_Database;

      $Qstock = $osC_Database->query('select products_quantity from :table_products where products_id = :products_id');
      $Qstock->bindTable(':table_products', TABLE_PRODUCTS);
      $Qstock->bindInt(':products_id', tep_get_prid($id));
      $Qstock->execute();

      if (($Qstock->valueInt('products_quantity') - $quantity) > 0) {
        return true;
      } elseif ($this->_products_out_of_stock === false) {
        $this->_products_out_of_stock = true;
      }

      return false;
    }

    function hasProductsOutOfStock() {
      return $this->_products_out_of_stock;
    }
  }
?>
