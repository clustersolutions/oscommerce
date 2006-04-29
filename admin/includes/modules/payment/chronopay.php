<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Payment_chronopay extends osC_Payment_Admin {
    var $_title,
        $_code = 'chronopay',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_status = false;

    function osC_Payment_chronopay() {
      global $osC_Language;

      $this->_title = $osC_Language->get('payment_chronopay_title');
      $this->_description = $osC_Language->get('payment_chronopay_description');
      $this->_method_title = $osC_Language->get('payment_chronopay_method_title');
      $this->_status = (defined('MODULE_PAYMENT_CHRONOPAY_STATUS') && (MODULE_PAYMENT_CHRONOPAY_STATUS == '1') ? true : false);
      $this->_sort_order = (defined('MODULE_PAYMENT_CHRONOPAY_SORT_ORDER') ? MODULE_PAYMENT_CHRONOPAY_SORT_ORDER : null);
    }

    function isInstalled() {
      return defined('MODULE_PAYMENT_CHRONOPAY_STATUS');
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Enable ChronoPay Payments', 'MODULE_PAYMENT_CHRONOPAY_STATUS', '-1', 'Do you want to accept ChronoPay payments?', '6', '0', 'osc_cfg_get_boolean_value', 'tep_cfg_select_option(array(1, -1), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('ChronoPay Product ID', 'MODULE_PAYMENT_CHRONOPAY_PRODUCT_ID', '', 'The product ID to assign transactions to.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('MD5 Hash Signature', 'MODULE_PAYMENT_CHRONOPAY_MD5_HASH', '', 'Use this value to verify transactions with.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Currency', 'MODULE_PAYMENT_CHRONOPAY_CURRENCY', 'USD', 'The currency to use for credit card transactions', '6', '0', 'tep_cfg_select_option(array(\'Selected Currency\',\'USD\',\'EUR\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_CHRONOPAY_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_CHRONOPAY_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_CHRONOPAY_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('MODULE_PAYMENT_CHRONOPAY_STATUS',
                             'MODULE_PAYMENT_CHRONOPAY_PRODUCT_ID',
                             'MODULE_PAYMENT_CHRONOPAY_MD5_HASH',
                             'MODULE_PAYMENT_CHRONOPAY_CURRENCY',
                             'MODULE_PAYMENT_CHRONOPAY_ZONE',
                             'MODULE_PAYMENT_CHRONOPAY_ORDER_STATUS_ID',
                             'MODULE_PAYMENT_CHRONOPAY_SORT_ORDER');
      }

      return $this->_keys;
    }
  }
?>
