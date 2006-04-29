<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_OrderTotal_shipping extends osC_OrderTotal {
    var $output;

    var $_title,
        $_code = 'shipping',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_status = false,
        $_sort_order;

    function osC_OrderTotal_shipping() {
      global $osC_Language, $osC_ShoppingCart;

      $this->output = array();

      $this->_title = $osC_Language->get('order_total_shipping_title');
      $this->_description = $osC_Language->get('order_total_shipping_description');
      $this->_status = (defined('MODULE_ORDER_TOTAL_SHIPPING_STATUS') && (MODULE_ORDER_TOTAL_SHIPPING_STATUS == 'true') ? true : false);
      $this->_sort_order = (defined('MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER') ? MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER : null);
    }

    function process() {
      global $osC_Tax, $osC_ShoppingCart, $osC_Currencies;

      if ($osC_ShoppingCart->hasShippingMethod()) {
        $osC_ShoppingCart->addToTotal($osC_ShoppingCart->getShippingMethod('cost'));

        if ($osC_ShoppingCart->getShippingMethod('tax_class_id') > 0) {
          $tax = $osC_Tax->getTaxRate($osC_ShoppingCart->getShippingMethod('tax_class_id'), $osC_ShoppingCart->getShippingAddress('country_id'), $osC_ShoppingCart->getShippingAddress('zone_id'));
          $tax_description = $osC_Tax->getTaxRateDescription($osC_ShoppingCart->getShippingMethod('tax_class_id'), $osC_ShoppingCart->getShippingAddress('country_id'), $osC_ShoppingCart->getShippingAddress('zone_id'));

          $osC_ShoppingCart->addTaxAmount(tep_calculate_tax($osC_ShoppingCart->getShippingMethod('cost'), $tax));
          $osC_ShoppingCart->addTaxGroup($tax_description, tep_calculate_tax($osC_ShoppingCart->getShippingMethod('cost'), $tax));

          if (DISPLAY_PRICE_WITH_TAX == '1') {
            $osC_ShoppingCart->addToTotal(tep_calculate_tax($osC_ShoppingCart->getShippingMethod('cost'), $tax));
            $osC_ShoppingCart->_shipping_method['cost'] += tep_calculate_tax($osC_ShoppingCart->getShippingMethod('cost'), $tax);
          }
        }

        $this->output[] = array('title' => $osC_ShoppingCart->getShippingMethod('title') . ':',
                                'text' => $osC_Currencies->format($osC_ShoppingCart->getShippingMethod('cost')),
                                'value' => $osC_ShoppingCart->getShippingMethod('cost'));
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $this->_check = defined('MODULE_ORDER_TOTAL_SHIPPING_STATUS');
      }

      return $this->_check;
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Shipping', 'MODULE_ORDER_TOTAL_SHIPPING_STATUS', 'true', 'Do you want to display the order shipping cost?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER', '2', 'Sort order of display.', '6', '2', now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('MODULE_ORDER_TOTAL_SHIPPING_STATUS',
                             'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER');
      }

      return $this->_keys;
    }
  }
?>
