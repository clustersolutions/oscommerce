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

  class osC_OrderTotal_tax extends osC_OrderTotal {
    var $output;

    var $_title,
        $_code = 'tax',
        $_status = false,
        $_sort_order;

    function osC_OrderTotal_tax() {
      global $osC_Language;

      $this->output = array();

      $this->_title = $osC_Language->get('order_total_tax_title');
      $this->_description = $osC_Language->get('order_total_tax_description');
      $this->_status = (defined('MODULE_ORDER_TOTAL_TAX_STATUS') && (MODULE_ORDER_TOTAL_TAX_STATUS == 'true') ? true : false);
      $this->_sort_order = (defined('MODULE_ORDER_TOTAL_TAX_SORT_ORDER') ? MODULE_ORDER_TOTAL_TAX_SORT_ORDER : null);
    }

    function process() {
      global $osC_ShoppingCart, $osC_Currencies;

      foreach ($osC_ShoppingCart->getTaxGroups() as $key => $value) {
        if ($value > 0) {
          if (DISPLAY_PRICE_WITH_TAX == '1') {
            $osC_ShoppingCart->addToTotal($value);
          }

          $this->output[] = array('title' => $key . ':',
                                  'text' => $osC_Currencies->format($value),
                                  'value' => $value);
        }
      }
    }
  }
?>
