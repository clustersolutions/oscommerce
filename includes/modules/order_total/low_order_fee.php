<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_OrderTotal_low_order_fee extends osC_OrderTotal {
    var $output;

    var $_title,
        $_code = 'low_order_fee',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_status = false,
        $_sort_order;

    function osC_OrderTotal_low_order_fee() {
      global $osC_Language;

      $this->output = array();

      $this->_title = $osC_Language->get('order_total_loworderfee_title');
      $this->_description = $osC_Language->get('order_total_loworderfee_description');
      $this->_status = (defined('MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS') && (MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS == 'true') ? true : false);
      $this->_sort_order = (defined('MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER') ? MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER : null);
    }

    function process() {
      global $osC_Tax, $osC_ShoppingCart, $osC_Currencies;

      if (MODULE_ORDER_TOTAL_LOWORDERFEE_LOW_ORDER_FEE == 'true') {
        switch (MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION) {
          case 'national':
            if ($osC_ShoppingCart->getShippingAddress('country_id') == STORE_COUNTRY) {
              $pass = true;
            }
            break;

          case 'international':
            if ($osC_ShoppingCart->getShippingAddress('country_id') != STORE_COUNTRY) {
              $pass = true;
            }
            break;

          case 'both':
            $pass = true;
            break;

          default:
            $pass = false;
        }

        if ( ($pass == true) && ($osC_ShoppingCart->getSubTotal() < MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER) ) {
          $tax = $osC_Tax->getTaxRate(MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS, $osC_ShoppingCart->getTaxingAddress('country_id'), $osC_ShoppingCart->getTaxingAddress('zone_id'));
          $tax_description = $osC_Tax->getTaxRateDescription(MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS, $osC_ShoppingCart->getTaxingAddress('country_id'), $osC_ShoppingCart->getTaxingAddress('zone_id'));

          $osC_ShoppingCart->addTaxAmount(tep_calculate_tax(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax));
          $osC_ShoppingCart->addTaxGroup($tax_description, tep_calculate_tax(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax));
          $osC_ShoppingCart->addToTotal(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE + tep_calculate_tax(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax));

          $this->output[] = array('title' => $this->_title . ':',
                                  'text' => $osC_Currencies->format(tep_add_tax(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax)),
                                  'value' => tep_add_tax(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax));
        }
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $this->_check = defined('MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS');
      }

      return $this->_check;
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Low Order Fee', 'MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS', 'true', 'Do you want to display the low order fee?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER', '4', 'Sort order of display.', '6', '2', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Allow Low Order Fee', 'MODULE_ORDER_TOTAL_LOWORDERFEE_LOW_ORDER_FEE', 'false', 'Do you want to allow low order fees?', '6', '3', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added) values ('Order Fee For Orders Under', 'MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER', '50', 'Add the low order fee to orders under this amount.', '6', '4', 'currencies->format', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added) values ('Order Fee', 'MODULE_ORDER_TOTAL_LOWORDERFEE_FEE', '5', 'Low order fee.', '6', '5', 'currencies->format', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Attach Low Order Fee On Orders Made', 'MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION', 'both', 'Attach low order fee for orders sent to the set destination.', '6', '6', 'tep_cfg_select_option(array(\'national\', \'international\', \'both\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS', '0', 'Use the following tax class on the low order fee.', '6', '7', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS',
                             'MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER',
                             'MODULE_ORDER_TOTAL_LOWORDERFEE_LOW_ORDER_FEE',
                             'MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER',
                             'MODULE_ORDER_TOTAL_LOWORDERFEE_FEE',
                             'MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION',
                             'MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS');
      }

      return $this->_keys;
    }
  }
?>
