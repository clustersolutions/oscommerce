<?php
/*
  $Id: secpay.php,v 1.37 2004/07/22 21:57:56 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class secpay {
    var $code, $title, $description, $sort_order, $enabled = false;

    function secpay() {
      $this->code = 'secpay';
      $this->title = MODULE_PAYMENT_SECPAY_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_SECPAY_TEXT_DESCRIPTION;

      if (defined('MODULE_PAYMENT_SECPAY_STATUS')) {
        $this->initialize();
      }
    }

    function initialize() {
      global $order;

      $this->sort_order = MODULE_PAYMENT_SECPAY_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_SECPAY_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_SECPAY_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_SECPAY_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = 'https://www.secpay.com/java-bin/ValCard';
    }

    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_SECPAY_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_SECPAY_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
      return array('id' => $this->code,
                   'module' => $this->title);
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
      return false;
    }

    function process_button() {
      global $osC_Session, $order, $osC_Currencies;

      switch (MODULE_PAYMENT_SECPAY_CURRENCY) {
        case 'Default Currency':
          $sec_currency = DEFAULT_CURRENCY;
          break;
        case 'Any Currency':
        default:
          $sec_currency = $osC_Session->value('currency');
          break;
      }

      switch (MODULE_PAYMENT_SECPAY_TEST_STATUS) {
        case 'Always Fail':
          $test_status = 'false';
          break;
        case 'Production':
          $test_status = 'live';
          break;
        case 'Always Successful':
        default:
          $test_status = 'true';
          break;
      }

      for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
        $order_details .= 'prod=' . $order->products[$i]['name'] . ',item_amout=' . number_format($order->products[$i]['final_price'] * $osC_Currencies->value($sec_currency), $osC_Currencies->currencies[$sec_currency]['decimal_places'], '.', '') . 'x' . $order->products[$i]['qty'] . ';';
      }

      $order_details .= 'TAX=' . number_format($order->info['tax'] * $osC_Currencies->value($sec_currency), $osC_Currencies->currencies[$sec_currency]['decimal_places'], '.', '') . ';';
      $order_details .= 'SHIPPING=' . number_format($order->info['shipping_cost'] * $osC_Currencies->value($sec_currency), $osC_Currencies->currencies[$sec_currency]['decimal_places'], '.', '') . ';';

      $trans_id = STORE_NAME . date('Ymdhis');
      $digest = md5($trans_id . number_format($order->info['total'] * $osC_Currencies->value($sec_currency), $osC_Currencies->currencies[$sec_currency]['decimal_places'], '.', '') . MODULE_PAYMENT_SECPAY_DIGEST_KEY);

      $process_button_string = osc_draw_hidden_field('merchant', MODULE_PAYMENT_SECPAY_MERCHANT_ID) .
                               osc_draw_hidden_field('trans_id', $trans_id) .
                               osc_draw_hidden_field('amount', number_format($order->info['total'] * $osC_Currencies->value($sec_currency), $osC_Currencies->currencies[$sec_currency]['decimal_places'], '.', '')) .
                               osc_draw_hidden_field('bill_name', $order->billing['firstname'] . ' ' . $order->billing['lastname']) .
                               osc_draw_hidden_field('bill_addr_1', $order->billing['street_address']) .
                               osc_draw_hidden_field('bill_addr_2', $order->billing['suburb']) .
                               osc_draw_hidden_field('bill_city', $order->billing['city']) .
                               osc_draw_hidden_field('bill_state', $order->billing['state']) .
                               osc_draw_hidden_field('bill_post_code', $order->billing['postcode']) .
                               osc_draw_hidden_field('bill_country', $order->billing['country']['title']) .
                               osc_draw_hidden_field('bill_tel', $order->customer['telephone']) .
                               osc_draw_hidden_field('bill_email', $order->customer['email_address']) .
                               osc_draw_hidden_field('ship_name', $order->delivery['firstname'] . ' ' . $order->delivery['lastname']) .
                               osc_draw_hidden_field('ship_addr_1', $order->delivery['street_address']) .
                               osc_draw_hidden_field('ship_addr_2', $order->delivery['suburb']) .
                               osc_draw_hidden_field('ship_city', $order->delivery['city']) .
                               osc_draw_hidden_field('ship_state', $order->delivery['state']) .
                               osc_draw_hidden_field('ship_post_code', $order->delivery['postcode']) .
                               osc_draw_hidden_field('ship_country', $order->delivery['country']['title']) .
                               osc_draw_hidden_field('currency', $sec_currency) .
                               osc_draw_hidden_field('order', $order_details) .
                               osc_draw_hidden_field('digest', $digest) .
                               osc_draw_hidden_field('callback', tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL', false) . ';' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'SSL', false)) .
                               osc_draw_hidden_field('backcallback', tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', false)) .
                               osc_draw_hidden_field($osC_Session->name, $osC_Session->id) .
                               osc_draw_hidden_field('options', 'test_status=' . $test_status . ',dups=false,cb_flds=' . $osC_Session->name);

      return $process_button_string;
    }

    function before_process() {
      global $osC_Session;

      if (PHP_VERSION < 4.1) {
        global $_GET;
      }

      if ($_GET['valid'] == 'true') {
        list($REQUEST_URI) = split("hash=", $_SERVER['REQUEST_URI']);
        if ($_GET['hash'] != MD5($REQUEST_URI . MODULE_PAYMENT_SECPAY_DIGEST_KEY)) {
          tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $osC_Session->name . '=' . $_GET[$osC_Session->name] . '&payment_error=' . $this->code, 'SSL', false, false));
        }
      } else {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $osC_Session->name . '=' . $_GET[$osC_Session->name] . '&payment_error=' . $this->code, 'SSL', false, false));
      }
    }

    function after_process() {
      return false;
    }

    function get_error() {
      if (PHP_VERSION < 4.1) {
        global $_GET;
      }

      if (isset($_GET['message']) && (strlen($_GET['message']) > 0)) {
        $error = urldecode($_GET['message']);
      } else {
        $error = MODULE_PAYMENT_SECPAY_TEXT_ERROR_MESSAGE;
      }

      return array('title' => MODULE_PAYMENT_SECPAY_TEXT_ERROR,
                   'error' => $error);
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_SECPAY_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable SECpay Module', 'MODULE_PAYMENT_SECPAY_STATUS', 'True', 'Do you want to accept SECPay payments?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Merchant ID', 'MODULE_PAYMENT_SECPAY_MERCHANT_ID', 'secpay', 'Merchant ID to use for the SECPay service', '6', '2', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Currency', 'MODULE_PAYMENT_SECPAY_CURRENCY', 'Any Currency', 'The currency to use for credit card transactions', '6', '3', 'tep_cfg_select_option(array(\'Any Currency\', \'Default Currency\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Mode', 'MODULE_PAYMENT_SECPAY_TEST_STATUS', 'Always Successful', 'Transaction mode to use for the SECPay service', '6', '4', 'tep_cfg_select_option(array(\'Always Successful\', \'Always Fail\', \'Production\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_SECPAY_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '5', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_SECPAY_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '6', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_SECPAY_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '7', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Digest Key', 'MODULE_PAYMENT_SECPAY_DIGEST_KEY', 'secpay', 'Key to use for the digest functionality', '6', '8', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_SECPAY_STATUS', 'MODULE_PAYMENT_SECPAY_MERCHANT_ID', 'MODULE_PAYMENT_SECPAY_CURRENCY', 'MODULE_PAYMENT_SECPAY_TEST_STATUS', 'MODULE_PAYMENT_SECPAY_ZONE', 'MODULE_PAYMENT_SECPAY_ORDER_STATUS_ID', 'MODULE_PAYMENT_SECPAY_SORT_ORDER', 'MODULE_PAYMENT_SECPAY_DIGEST_KEY');
    }
  }
?>
