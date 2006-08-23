<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
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

      foreach ($osC_ShoppingCart->_tax_groups as $key => $value) {
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

    function check() {
      if (!isset($this->_check)) {
        $this->_check = defined('MODULE_ORDER_TOTAL_TAX_STATUS');
      }

      return $this->_check;
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Tax', 'MODULE_ORDER_TOTAL_TAX_STATUS', 'true', 'Do you want to display the order tax value?', '6', '1', 'osc_cfg_set_boolean_value(array(\'true\', \'false\'))', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_TAX_SORT_ORDER', '3', 'Sort order of display.', '6', '2', now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('MODULE_ORDER_TOTAL_TAX_STATUS',
                             'MODULE_ORDER_TOTAL_TAX_SORT_ORDER');
      }

      return $this->_keys;
    }
  }
?>
