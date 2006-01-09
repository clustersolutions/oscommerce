<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class ipayment {
    var $code, $title, $description, $sort_order, $enabled = false;

    function ipayment() {
      $this->code = 'ipayment';
      $this->title = MODULE_PAYMENT_IPAYMENT_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_IPAYMENT_TEXT_DESCRIPTION;

      if (defined('MODULE_PAYMENT_IPAYMENT_STATUS')) {
        $this->initialize();
      }
    }

    function initialize() {
      global $order;

      $this->sort_order = MODULE_PAYMENT_IPAYMENT_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_IPAYMENT_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = 'https://ipayment.de/merchant/' . MODULE_PAYMENT_IPAYMENT_ID . '/processor.php';
    }

    function update_status() {
      global $osC_Database, $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_IPAYMENT_ZONE > 0) ) {
        $check_flag = false;

        $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
        $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qcheck->bindInt(':geo_zone_id', MODULE_PAYMENT_IPAYMENT_ZONE);
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
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      $js = '  if (payment_value == "' . $this->code . '") {' . "\n" .
            '    var cc_owner = document.checkout_payment.ipayment_cc_owner.value;' . "\n" .
            '    var cc_number = document.checkout_payment.ipayment_cc_number.value;' . "\n" .
            '    if (cc_owner == "" || cc_owner.length < ' . CC_OWNER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_IPAYMENT_TEXT_JS_CC_OWNER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '    if (cc_number == "" || cc_number.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_IPAYMENT_TEXT_JS_CC_NUMBER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '  }' . "\n";

      return $js;
    }

    function selection() {
      global $order, $osC_Database;

      for ($i=1; $i < 13; $i++) {
        $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));
      }

      $today = getdate();
      for ($i=$today['year']; $i < $today['year']+10; $i++) {
        $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
      }

      $Qcredit_cards = $osC_Database->query('select credit_card_name, credit_card_code from :table_credit_cards where credit_card_status = :credit_card_status');

      $Qcredit_cards->bindRaw(':table_credit_cards', TABLE_CREDIT_CARDS);
      $Qcredit_cards->bindInt(':credit_card_status', '1');
      $Qcredit_cards->setCache('credit-cards');
      $Qcredit_cards->execute();

      while ($Qcredit_cards->next()) {
        $credit_cards[] = array('id' => $Qcredit_cards->value('credit_card_code'), 'text' => $Qcredit_cards->value('credit_card_name'));
      }

      $Qcredit_cards->freeResult();

      $selection = array('id' => $this->code,
                         'module' => $this->title,
                         'fields' => array(array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_OWNER,
                                                 'field' => osc_draw_input_field('ipayment_cc_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'])),
                                           array('title' => MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_TYPE,
                                                 'field' => osc_draw_pull_down_menu('ipayment_cc_type', $credit_cards)),
                                           array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_NUMBER,
                                                 'field' => osc_draw_input_field('ipayment_cc_number')),
                                           array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_EXPIRES,
                                                 'field' => osc_draw_pull_down_menu('ipayment_cc_expires_month', $expires_month) . '&nbsp;' . osc_draw_pull_down_menu('ipayment_cc_expires_year', $expires_year)),
                                           array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_CHECKNUMBER,
                                                 'field' => osc_draw_input_field('ipayment_cc_checkcode', '', 'size="4" maxlength="4"') . '&nbsp;<small>' . MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_CHECKNUMBER_LOCATION . '</small>')));

      return $selection;
    }

    function pre_confirmation_check() {
      global $messageStack;

      if (!tep_validate_credit_card($_POST['ipayment_cc_number'])) {
        $messageStack->add_session('checkout_payment', TEXT_CCVAL_ERROR_INVALID_NUMBER, 'error');

        $payment_error_return = 'ipayment_cc_owner=' . urlencode($_POST['ipayment_cc_owner']) . '&ipayment_cc_expires_month=' . urlencode($_POST['ipayment_cc_expires_month']) . '&ipayment_cc_expires_year=' . urlencode($_POST['ipayment_cc_expires_year']) . '&ipayment_cc_checkcode=' . urlencode($_POST['ipayment_cc_checkcode']);

        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment&' . $payment_error_return, 'SSL'));
      }

      $this->cc_card_owner = $_POST['ipayment_cc_owner'];
      $this->cc_card_type = $_POST['ipayment_cc_type'];
      $this->cc_card_number = $_POST['ipayment_cc_number'];
      $this->cc_expiry_month = $_POST['ipayment_cc_expires_month'];
      $this->cc_expiry_year = $_POST['ipayment_cc_expires_year'];
      $this->cc_checkcode = $_POST['ipayment_cc_checkcode'];
    }

    function confirmation() {
      $confirmation = array('title' => $this->title . ': ' . $this->cc_card_type,
                            'fields' => array(array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_OWNER,
                                                    'field' => $this->cc_card_owner),
                                              array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_NUMBER,
                                                    'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4)),
                                              array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_EXPIRES,
                                                    'field' => strftime('%B, %Y', mktime(0,0,0,$this->cc_expiry_month, 1, '20' . $this->cc_expiry_year)))));

      if (tep_not_null($this->cc_checkcode)) {
        $confirmation['fields'][] = array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_CHECKNUMBER,
                                          'field' => $this->cc_checkcode);
      }

      return $confirmation;
    }

    function process_button() {
      global $order, $osC_Currencies, $osC_Language;

      switch (MODULE_PAYMENT_IPAYMENT_CURRENCY) {
        case 'Always EUR':
          $trx_currency = 'EUR';
          break;
        case 'Always USD':
          $trx_currency = 'USD';
          break;
        case 'Either EUR or USD, else EUR':
          if ( ($_SESSION['currency'] == 'EUR') || ($_SESSION['currency'] == 'USD') ) {
            $trx_currency = $_SESSION['currency'];
          } else {
            $trx_currency = 'EUR';
          }
          break;
        case 'Either EUR or USD, else USD':
          if ( ($_SESSION['currency'] == 'EUR') || ($_SESSION['currency'] == 'USD') ) {
            $trx_currency = $_SESSION['currency'];
          } else {
            $trx_currency = 'USD';
          }
          break;
      }

      $payment_error_return = 'ipayment_cc_owner=' . urlencode($_POST['ipayment_cc_owner']) . '&ipayment_cc_expires_month=' . urlencode($_POST['ipayment_cc_expires_month']) . '&ipayment_cc_expires_year=' . urlencode($_POST['ipayment_cc_expires_year']) . '&ipayment_cc_checkcode=' . urlencode($_POST['ipayment_cc_checkcode']);

      $process_button_string = osc_draw_hidden_field('trxuser_id', MODULE_PAYMENT_IPAYMENT_USER_ID) .
                               osc_draw_hidden_field('trxpassword', MODULE_PAYMENT_IPAYMENT_PASSWORD) .
                               osc_draw_hidden_field('trx_amount', number_format($order->info['total'] * 100 * $osC_Currencies->value($trx_currency), 0, '','')) .
                               osc_draw_hidden_field('trx_currency', $trx_currency) .
                               osc_draw_hidden_field('trx_paymenttyp', 'cc') .
                               osc_draw_hidden_field('addr_name', $this->cc_card_owner) .
                               osc_draw_hidden_field('addr_street', $order->billing['street_address']) .
                               osc_draw_hidden_field('addr_city', $order->billing['city']) .
                               osc_draw_hidden_field('addr_zip', $order->billing['postcode']) .
                               osc_draw_hidden_field('addr_country', $order->billing['country']['iso_code_2']) .
                               osc_draw_hidden_field('addr_telefon', $order->customer['telephone']) .
                               osc_draw_hidden_field('addr_email', $order->customer['email_address']) .
                               osc_draw_hidden_field('error_lang', ($osC_Language->getCode() == 'en') ? 'en' : 'de') .
                               osc_draw_hidden_field('silent', '1') .
                               osc_draw_hidden_field('silent_error_url', tep_href_link(FILENAME_CHECKOUT, 'payment&payment_error=' . $this->code . '&' . $payment_error_return, 'SSL')) .
                               osc_draw_hidden_field('redirect_url', tep_href_link(FILENAME_CHECKOUT, 'process', 'SSL')) .
                               osc_draw_hidden_field('cc_number', $this->cc_card_number) .
                               osc_draw_hidden_field('cc_expdate_month', $this->cc_expiry_month) .
                               osc_draw_hidden_field('cc_expdate_year', $this->cc_expiry_year);


      if (tep_not_null($this->cc_checkcode)) {
        $process_button_string .= osc_draw_hidden_field('cc_checkcode', $this->cc_checkcode);
      }

      if (tep_not_null(MODULE_PAYMENT_IPAYMENT_SECURITY_KEY)) {
        $process_button_string .= osc_draw_hidden_field('trx_securityhash', md5(MODULE_PAYMENT_IPAYMENT_USER_ID . number_format($order->info['total'] * 100 * $osC_Currencies->value($trx_currency), 0, '','') . $trx_currency . MODULE_PAYMENT_IPAYMENT_PASSWORD . MODULE_PAYMENT_IPAYMENT_SECURITY_KEY));
      }

      return $process_button_string;
    }

    function before_process() {
      return false;
    }

    function after_process() {
      return false;
    }

    function get_error() {
      $error = array('title' => IPAYMENT_ERROR_HEADING,
                     'error' => (isset($_GET['ret_errormsg']) ? urldecode($_GET['ret_errormsg']) : IPAYMENT_ERROR_MESSAGE));

      return $error;
    }

    function check() {
      if (!isset($this->_check)) {
        $this->_check = defined('MODULE_PAYMENT_IPAYMENT_STATUS');
      }

      return $this->_check;
    }

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable iPayment Module', 'MODULE_PAYMENT_IPAYMENT_STATUS', 'True', 'Do you want to accept iPayment payments?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Account Number', 'MODULE_PAYMENT_IPAYMENT_ID', '99999', 'The account number used for the iPayment service', '6', '2', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('User ID', 'MODULE_PAYMENT_IPAYMENT_USER_ID', '99999', 'The user ID for the iPayment service', '6', '3', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('User Password', 'MODULE_PAYMENT_IPAYMENT_PASSWORD', '0', 'The user password for the iPayment service', '6', '4', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Security Key', 'MODULE_PAYMENT_IPAYMENT_SECURITY_KEY', '', 'The security key used to generate the security hash', '6', '5', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Currency', 'MODULE_PAYMENT_IPAYMENT_CURRENCY', 'Either EUR or USD, else EUR', 'The currency to use for credit card transactions', '6', '6', 'tep_cfg_select_option(array(\'Always EUR\', \'Always USD\', \'Either EUR or USD, else EUR\', \'Either EUR or USD, else USD\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_IPAYMENT_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '7', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_IPAYMENT_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '8', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '9', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    }

    function remove() {
      global $osC_Database;

      $Qdel = $osC_Database->query('delete from :table_configuration where configuration_key in (":configuration_key")');
      $Qdel->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qdel->bindRaw(':configuration_key', implode('", "', $this->keys()));
      $Qdel->execute();
    }

    function keys() {
      return array('MODULE_PAYMENT_IPAYMENT_STATUS', 'MODULE_PAYMENT_IPAYMENT_ID', 'MODULE_PAYMENT_IPAYMENT_USER_ID', 'MODULE_PAYMENT_IPAYMENT_PASSWORD', 'MODULE_PAYMENT_IPAYMENT_SECURITY_KEY', 'MODULE_PAYMENT_IPAYMENT_CURRENCY', 'MODULE_PAYMENT_IPAYMENT_ZONE', 'MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID', 'MODULE_PAYMENT_IPAYMENT_SORT_ORDER');
    }
  }
?>
