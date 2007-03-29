<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_OrderTotal_shipping extends osC_OrderTotal {
    var $output;

    var $_title,
        $_code = 'shipping',
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

          $osC_ShoppingCart->addTaxAmount($osC_Tax->calculate($osC_ShoppingCart->getShippingMethod('cost'), $tax));
          $osC_ShoppingCart->addTaxGroup($tax_description, $osC_Tax->calculate($osC_ShoppingCart->getShippingMethod('cost'), $tax));

          if (DISPLAY_PRICE_WITH_TAX == '1') {
            $osC_ShoppingCart->addToTotal($osC_Tax->calculate($osC_ShoppingCart->getShippingMethod('cost'), $tax));
            $osC_ShoppingCart->_shipping_method['cost'] += $osC_Tax->calculate($osC_ShoppingCart->getShippingMethod('cost'), $tax);
          }
        }

        $this->output[] = array('title' => $osC_ShoppingCart->getShippingMethod('title') . ':',
                                'text' => $osC_Currencies->format($osC_ShoppingCart->getShippingMethod('cost')),
                                'value' => $osC_ShoppingCart->getShippingMethod('cost'));
      }
    }
  }
?>
