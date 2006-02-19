<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Payment_paypal extends osC_Payment {
    var $_title,
        $_code = 'paypal',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_status = false,
        $_sort_order;

    function osC_Payment_paypal() {
      global $osC_Language;

      $this->_title = $osC_Language->get('payment_paypal_title');
      $this->_description = $osC_Language->get('payment_paypal_description');
      $this->_status = (defined('MODULE_PAYMENT_PAYPAL_STATUS') && (MODULE_PAYMENT_PAYPAL_STATUS == 'True') ? true : false);
      $this->_sort_order = (defined('MODULE_PAYMENT_PAYPAL_SORT_ORDER') ? MODULE_PAYMENT_PAYPAL_SORT_ORDER : null);

      if (defined('MODULE_PAYMENT_PAYPAL_STATUS')) {
        $this->initialize();
      }
    }

    function initialize() {
      global $order;

      if ((int)MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = 'https://www.paypal.com/cgi-bin/webscr';
    }

    function update_status() {
      global $osC_Database, $order;

      if ( ($this->_status === true) && ((int)MODULE_PAYMENT_PAYPAL_ZONE > 0) ) {
        $check_flag = false;

        $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
        $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qcheck->bindInt(':geo_zone_id', MODULE_PAYMENT_PAYPAL_ZONE);
        $Qcheck->bindInt(':zone_country_id', $order->billing['country']['id']);
        $Qcheck->execute();

        while ($Qcheck->next()) {
          if ($Qcheck->valueInt('zone_id') < 1) {
            $check_flag = true;
            break;
          } elseif ($Qcheck->valueInt('zone_id') == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->_status = false;
        }
      }
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
      return array('id' => $this->_code,
                   'module' => $this->_title);
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
      return false;
    }

    function process_button() {
      global $order, $osC_Currencies;

      if (MODULE_PAYMENT_PAYPAL_CURRENCY == 'Selected Currency') {
        $my_currency = $_SESSION['currency'];
      } else {
        $my_currency = substr(MODULE_PAYMENT_PAYPAL_CURRENCY, 5);
      }
      if (!in_array($my_currency, array('CAD', 'EUR', 'GBP', 'JPY', 'USD'))) {
        $my_currency = 'USD';
      }
      $process_button_string = osc_draw_hidden_field('cmd', '_xclick') .
                               osc_draw_hidden_field('business', MODULE_PAYMENT_PAYPAL_ID) .
                               osc_draw_hidden_field('item_name', STORE_NAME) .
                               osc_draw_hidden_field('amount', number_format(($order->info['total'] - $order->info['shipping_cost']) * $osC_Currencies->value($my_currency), $osC_Currencies->decimalPlaces($my_currency))) .
                               osc_draw_hidden_field('first_name', $order->billing['firstname']) .
                               osc_draw_hidden_field('last_name', $order->billing['lastname']) .
                               osc_draw_hidden_field('address1', $order->billing['street_address']) .
                               osc_draw_hidden_field('address2', $order->billing['suburb']) .
                               osc_draw_hidden_field('city', $order->billing['city']) .
                               osc_draw_hidden_field('state', $order->billing['state']) .
                               osc_draw_hidden_field('zip', $order->billing['postcode']) .
                               osc_draw_hidden_field('lc', $order->billing['country']['iso_code_2']) .
                               osc_draw_hidden_field('email', $order->customer['email_address']) .
                               osc_draw_hidden_field('shipping', number_format($order->info['shipping_cost'] * $osC_Currencies->value($my_currency), $osC_Currencies->decimalPlaces($my_currency))) .
                               osc_draw_hidden_field('currency_code', $my_currency) .
                               osc_draw_hidden_field('return', tep_href_link(FILENAME_CHECKOUT, 'process', 'SSL')) .
                               osc_draw_hidden_field('rm', '2') .
                               tep_draw_hidden_field('bn', 'osCommerce') .
                               osc_draw_hidden_field('no_note', '1') .
                               osc_draw_hidden_field('cancel_return', tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));

      return $process_button_string;
    }

    function before_process() {
      return false;
    }

    function after_process() {
      return false;
    }

    function output_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $this->_check = defined('MODULE_PAYMENT_PAYPAL_STATUS');
      }

      return $this->_check;
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable PayPal Module', 'MODULE_PAYMENT_PAYPAL_STATUS', 'True', 'Do you want to accept PayPal payments?', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('E-Mail Address', 'MODULE_PAYMENT_PAYPAL_ID', 'you@yourbusiness.com', 'The e-mail address to use for the PayPal service', '6', '4', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Currency', 'MODULE_PAYMENT_PAYPAL_CURRENCY', 'Selected Currency', 'The currency to use for credit card transactions', '6', '6', 'tep_cfg_select_option(array(\'Selected Currency\',\'Only USD\',\'Only CAD\',\'Only EUR\',\'Only GBP\',\'Only JPY\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_PAYPAL_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_PAYPAL_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('MODULE_PAYMENT_PAYPAL_STATUS',
                             'MODULE_PAYMENT_PAYPAL_ID',
                             'MODULE_PAYMENT_PAYPAL_CURRENCY',
                             'MODULE_PAYMENT_PAYPAL_ZONE',
                             'MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID',
                             'MODULE_PAYMENT_PAYPAL_SORT_ORDER');
      }

      return $this->_keys;
    }
  }
?>
