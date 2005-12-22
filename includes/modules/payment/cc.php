<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class cc {
    var $code, $title, $description, $sort_order, $enabled = false;

    function cc() {
      $this->code = 'cc';
      $this->title = MODULE_PAYMENT_CC_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_CC_TEXT_DESCRIPTION;

      if (defined('MODULE_PAYMENT_CC_STATUS')) {
        $this->initialize();
      }
    }

    function initialize() {
      global $order;

      $this->sort_order = MODULE_PAYMENT_CC_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_CC_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_CC_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_CC_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();
    }

    function update_status() {
      global $osC_Database, $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_CC_ZONE > 0) ) {
        $check_flag = false;

        $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
        $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qcheck->bindInt(':geo_zone_id', MODULE_PAYMENT_CC_ZONE);
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
            '    var cc_owner = document.checkout_payment.cc_owner.value;' . "\n" .
            '    var cc_number = document.checkout_payment.cc_number.value;' . "\n" .
            '    if (cc_owner == "" || cc_owner.length < ' . CC_OWNER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_CC_TEXT_JS_CC_OWNER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '    if (cc_number == "" || cc_number.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_CC_TEXT_JS_CC_NUMBER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '  }' . "\n";

      return $js;
    }

    function selection() {
      global $order, $osC_Database;

      for ($i=1; $i<13; $i++) {
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
                         'fields' => array(array('title' => MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_OWNER,
                                                 'field' => osc_draw_input_field('cc_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'])),
                                           array('title' => MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_TYPE,
                                                 'field' => osc_draw_pull_down_menu('cc_type', $credit_cards)),
                                           array('title' => MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_NUMBER,
                                                 'field' => osc_draw_input_field('cc_number')),
                                           array('title' => MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_EXPIRES,
                                                 'field' => osc_draw_pull_down_menu('cc_expires_month', $expires_month) . '&nbsp;' . osc_draw_pull_down_menu('cc_expires_year', $expires_year))));

      return $selection;
    }

    function pre_confirmation_check() {
      global $messageStack;

      if (!tep_validate_credit_card($_POST['cc_number'])) {
        $messageStack->add_session('checkout_payment', TEXT_CCVAL_ERROR_INVALID_NUMBER, 'error');

        $payment_error_return = 'cc_owner=' . urlencode($_POST['cc_owner']) . '&cc_expires_month=' . urlencode($_POST['cc_expires_month']) . '&cc_expires_year=' . urlencode($_POST['cc_expires_year']);

        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'payment&' . $payment_error_return, 'SSL'));
      }

      $this->cc_card_type = $_POST['cc_type'];
      $this->cc_card_number = $_POST['cc_number'];
    }

    function confirmation() {
      $confirmation = array('title' => $this->title . ': ' . $this->cc_card_type,
                            'fields' => array(array('title' => MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_OWNER,
                                                    'field' => $_POST['cc_owner']),
                                              array('title' => MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_NUMBER,
                                                    'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4)),
                                              array('title' => MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_EXPIRES,
                                                    'field' => strftime('%B, %Y', mktime(0,0,0,$_POST['cc_expires_month'], 1, '20' . $_POST['cc_expires_year'])))));

      return $confirmation;
    }

    function process_button() {
      $process_button_string = osc_draw_hidden_field('cc_owner', $_POST['cc_owner']) .
                               osc_draw_hidden_field('cc_expires', $_POST['cc_expires_month'] . $_POST['cc_expires_year']) .
                               osc_draw_hidden_field('cc_type', $this->cc_card_type) .
                               osc_draw_hidden_field('cc_number', $this->cc_card_number);

      return $process_button_string;
    }

    function before_process() {
      global $order;

      if ( (defined('MODULE_PAYMENT_CC_EMAIL')) && (tep_validate_email(MODULE_PAYMENT_CC_EMAIL)) ) {
        $len = strlen($_POST['cc_number']);

        $this->cc_middle = substr($_POST['cc_number'], 4, ($len-8));
        $order->info['cc_number'] = substr($_POST['cc_number'], 0, 4) . str_repeat('X', (strlen($_POST['cc_number']) - 8)) . substr($_POST['cc_number'], -4);
      }
    }

    function after_process() {
      global $insert_id;

      if ( (defined('MODULE_PAYMENT_CC_EMAIL')) && (tep_validate_email(MODULE_PAYMENT_CC_EMAIL)) ) {
        $message = 'Order #' . $insert_id . "\n\n" . 'Middle: ' . $this->cc_middle . "\n\n";

        tep_mail('', MODULE_PAYMENT_CC_EMAIL, 'Extra Order Info: #' . $insert_id, $message, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      }
    }

    function get_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $this->_check = defined('MODULE_PAYMENT_CC_STATUS');
      }

      return $this->_check;
    }

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Credit Card Module', 'MODULE_PAYMENT_CC_STATUS', 'True', 'Do you want to accept credit card payments?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Split Credit Card E-Mail Address', 'MODULE_PAYMENT_CC_EMAIL', '', 'If an e-mail address is entered, the middle digits of the credit card number will be sent to the e-mail address (the outside digits are stored in the database with the middle digits censored)', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_CC_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0' , now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_CC_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_CC_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    }

    function remove() {
      global $osC_Database;

      $Qdel = $osC_Database->query('delete from :table_configuration where configuration_key in (":configuration_key")');
      $Qdel->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qdel->bindRaw(':configuration_key', implode('", "', $this->keys()));
      $Qdel->execute();
    }

    function keys() {
      return array('MODULE_PAYMENT_CC_STATUS', 'MODULE_PAYMENT_CC_EMAIL', 'MODULE_PAYMENT_CC_ZONE', 'MODULE_PAYMENT_CC_ORDER_STATUS_ID', 'MODULE_PAYMENT_CC_SORT_ORDER');
    }
  }
?>
