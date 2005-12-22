<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Boxes_order_history extends osC_Modules {
    var $_title = 'Order History',
        $_code = 'order_history',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_order_history() {
//      $this->_title = BOX_HEADING_CUSTOMER_ORDERS;
    }

    function initialize() {
      global $osC_Customer, $osC_Database;

      if ($osC_Customer->isLoggedOn()) {
        $Qorders = $osC_Database->query('select distinct op.products_id from :table_orders o, :table_orders_products op, :table_products p where o.customers_id = :customers_id and o.orders_id = op.orders_id and op.products_id = p.products_id and p.products_status = 1 group by products_id order by o.date_purchased desc limit :limit');
        $Qorders->bindTable(':table_orders', TABLE_ORDERS);
        $Qorders->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
        $Qorders->bindTable(':table_products', TABLE_PRODUCTS);
        $Qorders->bindInt(':customers_id', $osC_Customer->getID());
        $Qorders->bindInt(':limit', BOX_ORDER_HISTORY_MAX_LIST);
        $Qorders->execute();

        if ($Qorders->numberOfRows()) {
          $product_ids = '';

          while ($Qorders->next()) {
            $product_ids .= $Qorders->valueInt('products_id') . ',';
          }

          $product_ids = substr($product_ids, 0, -1);

          $data = '<table border="0" width="100%" cellspacing="0" cellpadding="1">' . "\n";

          $Qproducts = $osC_Database->query('select products_id, products_name, products_keyword from :table_products_description where products_id in (:products_id) and language_id = :language_id order by products_name');
          $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
          $Qproducts->bindRaw(':products_id', $product_ids);
          $Qproducts->bindInt(':language_id', $_SESSION['languages_id']);
          $Qproducts->execute();

          while ($Qproducts->next()) {
            $data .= '  <tr>' . "\n" .
                     '    <td class="infoBoxContents"><a href="' . tep_href_link(FILENAME_PRODUCTS, $Qproducts->value('products_keyword')) . '">' . $Qproducts->value('products_name') . '</a></td>' . "\n" .
                     '    <td class="infoBoxContents" align="right" valign="top"><a href="' . tep_href_link(basename($_SERVER['PHP_SELF']), tep_get_all_get_params(array('action')) . 'action=cust_order&pid=' . $Qproducts->valueInt('products_id')) . '">' . tep_image(DIR_WS_IMAGES . 'icons/cart.gif', ICON_CART) . '</a></td>' . "\n" .
                     '  </tr>' . "\n";
          }

          $data .= '</table>';

          $this->_content = $data;
        }
      }
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum List Size', 'BOX_ORDER_HISTORY_MAX_LIST', '5', 'Maximum amount of products to show in the listing', '6', '0', now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('BOX_ORDER_HISTORY_MAX_LIST');
      }

      return $this->_keys;
    }
  }
?>
