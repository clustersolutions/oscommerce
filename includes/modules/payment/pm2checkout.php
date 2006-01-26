<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class pm2checkout {
    var $code, $title, $description, $sort_order, $enabled = false;

    function pm2checkout() {
      global $osC_Language;

      $this->code = 'pm2checkout';
      $this->title = $osC_Language->get('payment_2checkout_title');
      $this->description = $osC_Language->get('payment_2checkout_description');

      if (defined('MODULE_PAYMENT_2CHECKOUT_STATUS')) {
        $this->initialize();
      }
    }

    function initialize() {
      global $order;

      $this->sort_order = MODULE_PAYMENT_2CHECKOUT_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_2CHECKOUT_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_2CHECKOUT_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_2CHECKOUT_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = 'https://www.2checkout.com/cgi-bin/Abuyers/purchase.2c';
    }

    function update_status() {
      global $osC_Database, $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_2CHECKOUT_ZONE > 0) ) {
        $check_flag = false;

        $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
        $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qcheck->bindInt(':geo_zone_id', MODULE_PAYMENT_2CHECKOUT_ZONE);
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
      global $osC_Language;

      $js = '  if (payment_value == "' . $this->code . '") {' . "\n" .
            '    var cc_number = document.checkout_payment.pm_2checkout_cc_number.value;' . "\n" .
            '    if (cc_number == "" || cc_number.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . sprintf($osC_Language->get('payment_2checkout_js_credit_card_number'), CC_NUMBER_MIN_LENGTH) . '\n";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '  }' . "\n";

      return $js;
    }

    function selection() {
      global $osC_Database, $osC_Language, $order;

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
                         'fields' => array(array('title' => $osC_Language->get('payment_2checkout_credit_card_owner_first_name'),
                                                 'field' => osc_draw_input_field('pm_2checkout_cc_owner_firstname', $order->billing['firstname'])),
                                           array('title' => $osC_Language->get('payment_2checkout_credit_card_owner_last_name'),
                                                 'field' => osc_draw_input_field('pm_2checkout_cc_owner_lastname', $order->billing['lastname'])),
                                           array('title' => $osC_Language->get('payment_2checkout_credit_cart_type'),
                                                 'field' => osc_draw_pull_down_menu('pm_2checkout_cc_type', $credit_cards)),
                                           array('title' => $osC_Language->get('payment_2checkout_credit_card_number'),
                                                 'field' => osc_draw_input_field('pm_2checkout_cc_number')),
                                           array('title' => $osC_Language->get('payment_2checkout_credit_card_expiry_date'),
                                                 'field' => osc_draw_pull_down_menu('pm_2checkout_cc_expires_month', $expires_month) . '&nbsp;' . osc_draw_pull_down_menu('pm_2checkout_cc_expires_year', $expires_year)),
                                           array('title' => $osC_Language->get('payment_2checkout_credit_card_checknumber'),
                                                 'field' => osc_draw_input_field('pm_2checkout_cc_cvv', '', 'size="4" maxlength="4"') . '&nbsp;<small>' . $osC_Language->get('payment_2checkout_credit_card_checknumber_location') . '</small>')));

      return $selection;
    }

    function pre_confirmation_check() {
      global $osC_Language, $messageStack;

      if (!tep_validate_credit_card($_POST['pm_2checkout_cc_number'])) {
        $messageStack->add_session('checkout_payment', $osC_Language->get('credit_card_number_error'), 'error');

        $payment_error_return = 'pm_2checkout_cc_owner_firstname=' . urlencode($_POST['pm_2checkout_cc_owner_firstname']) . '&pm_2checkout_cc_owner_lastname=' . urlencode($_POST['pm_2checkout_cc_owner_lastname']) . '&pm_2checkout_cc_expires_month=' . urlencode($_POST['pm_2checkout_cc_expires_month']) . '&pm_2checkout_cc_expires_year=' . urlencode($_POST['pm_2checkout_cc_expires_year']) . '&pm_2checkout_cc_cvv=' . urlencode($_POST['pm_2checkout_cc_cvv']);

        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment&' . $payment_error_return, 'SSL'));
      }

      $this->cc_card_type = $_POST['pm_2checkout_cc_type'];
      $this->cc_card_number = $_POST['pm_2checkout_cc_number'];
      $this->cc_expiry_month = $_POST['pm_2checkout_cc_expires_month'];
      $this->cc_expiry_year = $_POST['pm_2checkout_cc_expires_year'];
      $this->cc_checkcode = $_POST['pm_2checkout_cc_cvv'];
    }

    function confirmation() {
      global $osC_Language;

      $confirmation = array('title' => $this->title . ': ' . $this->cc_card_type,
                            'fields' => array(array('title' => $osC_Language->get('payment_2checkout_credit_card_owner'),
                                                    'field' => $_POST['pm_2checkout_cc_owner_firstname'] . ' ' . $_POST['pm_2checkout_cc_owner_lastname']),
                                              array('title' => $osC_Language->get('payment_2checkout_credit_card_number'),
                                                    'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4)),
                                              array('title' => $osC_Language->get('payment_2checkout_credit_card_expiry_date'),
                                                    'field' => strftime('%B, %Y', mktime(0,0,0,$this->cc_expiry_month, 1, '20' . $this->cc_expiry_year)))));

      if (tep_not_null($this->cc_checkcode)) {
        $confirmation['fields'][] = array('title' => $osC_Language->get('payment_2checkout_credit_card_checknumber'),
                                          'field' => $this->cc_checkcode);
      }

      return $confirmation;
    }

    function process_button() {
      global $order;

      $process_button_string = osc_draw_hidden_field('x_login', MODULE_PAYMENT_2CHECKOUT_LOGIN) .
                               osc_draw_hidden_field('x_amount', number_format($order->info['total'], 2)) .
                               osc_draw_hidden_field('x_invoice_num', date('YmdHis')) .
                               osc_draw_hidden_field('x_test_request', ((MODULE_PAYMENT_2CHECKOUT_TESTMODE == 'Test') ? 'Y' : 'N')) .
                               osc_draw_hidden_field('x_card_num', $this->cc_card_number) .
                               osc_draw_hidden_field('cvv', $this->cc_checkcode) .
                               osc_draw_hidden_field('x_exp_date', $this->cc_expiry_month . substr($this->cc_expiry_year, -2)) .
                               osc_draw_hidden_field('x_first_name', $_POST['pm_2checkout_cc_owner_firstname']) .
                               osc_draw_hidden_field('x_last_name', $_POST['pm_2checkout_cc_owner_lastname']) .
                               osc_draw_hidden_field('x_address', $order->customer['street_address']) .
                               osc_draw_hidden_field('x_city', $order->customer['city']) .
                               osc_draw_hidden_field('x_state', $order->customer['state']) .
                               osc_draw_hidden_field('x_zip', $order->customer['postcode']) .
                               osc_draw_hidden_field('x_country', $order->customer['country']['title']) .
                               osc_draw_hidden_field('x_email', $order->customer['email_address']) .
                               osc_draw_hidden_field('x_phone', $order->customer['telephone']) .
                               osc_draw_hidden_field('x_ship_to_first_name', $order->delivery['firstname']) .
                               osc_draw_hidden_field('x_ship_to_last_name', $order->delivery['lastname']) .
                               osc_draw_hidden_field('x_ship_to_address', $order->delivery['street_address']) .
                               osc_draw_hidden_field('x_ship_to_city', $order->delivery['city']) .
                               osc_draw_hidden_field('x_ship_to_state', $order->delivery['state']) .
                               osc_draw_hidden_field('x_ship_to_zip', $order->delivery['postcode']) .
                               osc_draw_hidden_field('x_ship_to_country', $order->delivery['country']['title']) .
                               osc_draw_hidden_field('x_receipt_link_url', tep_href_link(FILENAME_CHECKOUT, 'process', 'SSL')) .
                               osc_draw_hidden_field('x_email_merchant', ((MODULE_PAYMENT_2CHECKOUT_EMAIL_MERCHANT == 'True') ? 'TRUE' : 'FALSE'));

      return $process_button_string;
    }

    function before_process() {
      global $osC_Language, $messageStack;

      if ($_POST['x_response_code'] != '1') {
        $messageStack->add_session('checkout_payment', $osC_Language->get('payment_2checkout_error_message'), 'error');

        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));
      }
    }

    function after_process() {
      return false;
    }

    function get_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $this->_check = defined('MODULE_PAYMENT_2CHECKOUT_STATUS');
      }

      return $this->_check;
    }

    function install() {
      global $osC_Database, $osC_Language;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable 2CheckOut Module', 'MODULE_PAYMENT_2CHECKOUT_STATUS', 'True', 'Do you want to accept 2CheckOut payments?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Login/Store Number', 'MODULE_PAYMENT_2CHECKOUT_LOGIN', '18157', 'Login/Store Number used for the 2CheckOut service', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Mode', 'MODULE_PAYMENT_2CHECKOUT_TESTMODE', 'Test', 'Transaction mode used for the 2Checkout service', '6', '0', 'tep_cfg_select_option(array(\'Test\', \'Production\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Merchant Notifications', 'MODULE_PAYMENT_2CHECKOUT_EMAIL_MERCHANT', 'True', 'Should 2CheckOut e-mail a receipt to the store owner?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_2CHECKOUT_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_2CHECKOUT_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_2CHECKOUT_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");

      foreach ($osC_Language->getAll() as $key => $value) {
        foreach ($osC_Language->extractDefinitions($key . '/modules/payment/' . $this->code . '.xml') as $def) {
          $Qcheck = $osC_Database->query('select id from :table_languages_definitions where definition_key = :definition_key and content_group = :content_group and languages_id = :languages_id limit 1');
          $Qcheck->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
          $Qcheck->bindValue(':definition_key', $def['key']);
          $Qcheck->bindValue(':content_group', $def['group']);
          $Qcheck->bindInt(':languages_id', $value['id']);
          $Qcheck->execute();

          if ($Qcheck->numberOfRows() === 1) {
            $Qdef = $osC_Database->query('update :table_languages_definitions set definition_value = :definition_value where definition_key = :definition_key and content_group = :content_group and languages_id = :languages_id');
          } else {
            $Qdef = $osC_Database->query('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
          }
          $Qdef->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
          $Qdef->bindInt(':languages_id', $value['id']);
          $Qdef->bindValue(':content_group', $def['group']);
          $Qdef->bindValue(':definition_key', $def['key']);
          $Qdef->bindValue(':definition_value', $def['value']);
          $Qdef->execute();
        }
      }

      osC_Cache::clear('languages');
    }

    function remove() {
      global $osC_Database, $osC_Language;

      $Qdel = $osC_Database->query('delete from :table_configuration where configuration_key in (":configuration_key")');
      $Qdel->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qdel->bindRaw(':configuration_key', implode('", "', $this->keys()));
      $Qdel->execute();

      foreach ($osC_Language->extractDefinitions($osC_Language->getCode() . '/modules/payment/' . $this->code . '.xml') as $def) {
        $Qdel = $osC_Database->query('delete from :table_languages_definitions where definition_key = :definition_key and content_group = :content_group');
        $Qdel->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qdel->bindValue(':definition_key', $def['key']);
        $Qdel->bindValue(':content_group', $def['group']);
        $Qdel->execute();
      }

      osC_Cache::clear('languages');
    }

    function keys() {
      return array('MODULE_PAYMENT_2CHECKOUT_STATUS', 'MODULE_PAYMENT_2CHECKOUT_LOGIN', 'MODULE_PAYMENT_2CHECKOUT_TESTMODE', 'MODULE_PAYMENT_2CHECKOUT_EMAIL_MERCHANT', 'MODULE_PAYMENT_2CHECKOUT_ZONE', 'MODULE_PAYMENT_2CHECKOUT_ORDER_STATUS_ID', 'MODULE_PAYMENT_2CHECKOUT_SORT_ORDER');
    }
  }
?>
