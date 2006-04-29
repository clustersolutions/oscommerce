<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Payment_authorizenet_cc extends osC_Payment_Admin {
    var $_title,
        $_code = 'authorizenet_cc',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_status = false;

    function osC_Payment_authorizenet_cc() {
      global $osC_Language;

      $this->_title = $osC_Language->get('payment_authorizenet_cc_title');
      $this->_description = $osC_Language->get('payment_authorizenet_cc_description');
      $this->_method_title = $osC_Language->get('payment_authorizenet_cc_method_title');
      $this->_status = (defined('MODULE_PAYMENT_AUTHORIZENET_CC_STATUS') && (MODULE_PAYMENT_AUTHORIZENET_CC_STATUS == '1') ? true : false);
      $this->_sort_order = (defined('MODULE_PAYMENT_AUTHORIZENET_CC_SORT_ORDER') ? MODULE_PAYMENT_AUTHORIZENET_CC_SORT_ORDER : null);

      switch (MODULE_PAYMENT_AUTHORIZENET_CC_TRANSACTION_SERVER) {
        case 'production':
          $this->_gateway_url = 'https://secure.authorize.net:443/gateway/transact.dll';
          break;
        case 'certification':
          $this->_gateway_url = 'https://certification.authorize.net:443/gateway/transact.dll';
          break;
        default:
          $this->_gateway_url = 'https://test.authorize.net:443/gateway/transact.dll';
          break;
      }
    }

    function isInstalled() {
      return defined('MODULE_PAYMENT_AUTHORIZENET_CC_STATUS');
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Enable Authorize.net Credit Card Module', 'MODULE_PAYMENT_AUTHORIZENET_CC_STATUS', '-1', 'Do you want to accept Authorize.net credit card payments?', '6', '0', 'osc_cfg_get_boolean_value', 'tep_cfg_select_option(array(1, -1), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Login ID', 'MODULE_PAYMENT_AUTHORIZENET_CC_LOGIN_ID', '', 'The Authorize.net account login ID.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Key', 'MODULE_PAYMENT_AUTHORIZENET_CC_TRANSACTION_KEY', '', 'The transaction key obtained from the Merchant Interface.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('MD5 Hash Signature', 'MODULE_PAYMENT_AUTHORIZENET_CC_MD5_HASH', '', 'The MD5 hash value to verify the results of a transaction.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Credit Cards', 'MODULE_PAYMENT_AUTHORIZENET_CC_ACCEPTED_TYPES', '', 'Accept these credit card types for this payment method.', '6', '0', 'tep_cfg_checkboxes_credit_cards(', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Verify With CVC', 'MODULE_PAYMENT_AUTHORIZENET_CC_VERIFY_WITH_CVC', '1', 'Verify the credit card with the billing address with the Credit Card Verification Checknumber (CVC)?', '6', '0', 'osc_cfg_get_boolean_value', 'tep_cfg_select_option(array(1, -1), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Server', 'MODULE_PAYMENT_AUTHORIZENET_CC_TRANSACTION_SERVER', 'test', 'Perform transactions on the production server or on the testing server.', '6', '0', 'tep_cfg_select_option(array(\'production\', \'certification\', \'test\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Transaction Mode', 'MODULE_PAYMENT_AUTHORIZENET_CC_TRANSACTION_TEST_MODE', '-1', 'Perform test transactions only.', '6', '0', 'osc_cfg_get_boolean_value', 'tep_cfg_select_option(array(1, -1), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_AUTHORIZENET_CC_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_AUTHORIZENET_CC_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_AUTHORIZENET_CC_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0' , now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('MODULE_PAYMENT_AUTHORIZENET_CC_STATUS',
                             'MODULE_PAYMENT_AUTHORIZENET_CC_LOGIN_ID',
                             'MODULE_PAYMENT_AUTHORIZENET_CC_TRANSACTION_KEY',
                             'MODULE_PAYMENT_AUTHORIZENET_CC_MD5_HASH',
                             'MODULE_PAYMENT_AUTHORIZENET_CC_ACCEPTED_TYPES',
                             'MODULE_PAYMENT_AUTHORIZENET_CC_VERIFY_WITH_CVC',
                             'MODULE_PAYMENT_AUTHORIZENET_CC_TRANSACTION_SERVER',
                             'MODULE_PAYMENT_AUTHORIZENET_CC_TRANSACTION_TEST_MODE',
                             'MODULE_PAYMENT_AUTHORIZENET_CC_ZONE',
                             'MODULE_PAYMENT_AUTHORIZENET_CC_ORDER_STATUS_ID',
                             'MODULE_PAYMENT_AUTHORIZENET_CC_SORT_ORDER');
      }

      return $this->_keys;
    }

    function getPostTransactionActions($history) {
      $actions = array();

      if ( (in_array('3', $history) === false) && (in_array('2', $history) === false) ) {
        $actions[3] = 'approveTransaction';
      }

      if (in_array('2', $history) === false) {
        $actions[2] = 'cancelTransaction';
      }

      return $actions;
    }

    function approveTransaction($id) {
      global $osC_Database;

      $Qorder = $osC_Database->query('select transaction_return_value from :table_orders_transactions_history where orders_id = :orders_id and transaction_code = 1 order by date_added limit 1');
      $Qorder->bindTable(':table_orders_transactions_history', TABLE_ORDERS_TRANSACTIONS_HISTORY);
      $Qorder->bindInt(':orders_id', $id);
      $Qorder->execute();

      if ($Qorder->numberOfRows() === 1) {
        $inquiry_regs = preg_split("/,(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/", $Qorder->value('transaction_return_value'));
        foreach ($inquiry_regs as &$value) {
          $value = substr($value, 1, -1); // remove double quotes
        }

        $params = array('x_version' => '3.1',
                        'x_delim_data' => 'TRUE',
                        'x_delim_char' => ',',
                        'x_encap_char' => '"',
                        'x_type' => 'PRIOR_AUTH_CAPTURE',
                        'x_login' => MODULE_PAYMENT_AUTHORIZENET_CC_LOGIN_ID,
                        'x_tran_key' => MODULE_PAYMENT_AUTHORIZENET_CC_TRANSACTION_KEY,
                        'x_trans_id' => $inquiry_regs[6],
                        'x_amount' => $inquiry_regs[9]);

        $post_string = '';

        foreach ($params as $key => $value) {
          $post_string .= $key . '=' . urlencode(trim($value)) . '&';
        }

        $post_string = substr($post_string, 0, -1);

        $result = osC_Payment::sendTransactionToGateway($this->_gateway_url, $post_string);

        if (empty($result) === false) {
          $regs = preg_split("/,(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/", $result);
          foreach ($regs as &$value) {
            $value = substr($value, 1, -1); // remove double quotes
          }

          $transaction_return_status = $regs[0];

          if ($transaction_return_status == '1') {
            if (tep_not_null(MODULE_PAYMENT_AUTHORIZENET_CC_MD5_HASH)) {
              if ($regs[37] != strtoupper(md5(MODULE_PAYMENT_AUTHORIZENET_CC_MD5_HASH . MODULE_PAYMENT_AUTHORIZENET_CC_LOGIN_ID . $inquiry_regs[6] . $inquiry_regs[9]))) {
                $transaction_return_status = '0';
              }
            }
          } else {
            $transaction_return_status = '0';
          }

          $Qtransaction = $osC_Database->query('insert into :table_orders_transactions_history (orders_id, transaction_code, transaction_return_value, transaction_return_status, date_added) values (:orders_id, :transaction_code, :transaction_return_value, :transaction_return_status, now())');
          $Qtransaction->bindTable(':table_orders_transactions_history', TABLE_ORDERS_TRANSACTIONS_HISTORY);
          $Qtransaction->bindInt(':orders_id', $id);
          $Qtransaction->bindInt(':transaction_code', 3);
          $Qtransaction->bindValue(':transaction_return_value', $result);
          $Qtransaction->bindInt(':transaction_return_status', $transaction_return_status);
          $Qtransaction->execute();
        }
      }
    }

    function cancelTransaction($id) {
      global $osC_Database;

      $Qorder = $osC_Database->query('select transaction_return_value from :table_orders_transactions_history where orders_id = :orders_id and transaction_code = 1 order by date_added limit 1');
      $Qorder->bindTable(':table_orders_transactions_history', TABLE_ORDERS_TRANSACTIONS_HISTORY);
      $Qorder->bindInt(':orders_id', $id);
      $Qorder->execute();

      if ($Qorder->numberOfRows() === 1) {
        $inquiry_regs = preg_split("/,(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/", $Qorder->value('transaction_return_value'));
        foreach ($inquiry_regs as &$value) {
          $value = substr($value, 1, -1); // remove double quotes
        }

        $params = array('x_version' => '3.1',
                        'x_delim_data' => 'TRUE',
                        'x_delim_char' => ',',
                        'x_encap_char' => '"',
                        'x_type' => 'VOID',
                        'x_login' => MODULE_PAYMENT_AUTHORIZENET_CC_LOGIN_ID,
                        'x_tran_key' => MODULE_PAYMENT_AUTHORIZENET_CC_TRANSACTION_KEY,
                        'x_trans_id' => $inquiry_regs[6],
                        'x_amount' => $inquiry_regs[9]);

        $post_string = '';

        foreach ($params as $key => $value) {
          $post_string .= $key . '=' . urlencode(trim($value)) . '&';
        }

        $post_string = substr($post_string, 0, -1);

        $result = osC_Payment::sendTransactionToGateway($this->_gateway_url, $post_string);

        if (empty($result) === false) {
          $regs = preg_split("/,(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/", $result);
          foreach ($regs as &$value) {
            $value = substr($value, 1, -1); // remove double quotes
          }

          $transaction_return_status = $regs[0];

          if ($transaction_return_status == '1') {
            if (tep_not_null(MODULE_PAYMENT_AUTHORIZENET_CC_MD5_HASH)) {
              if ($regs[37] != strtoupper(md5(MODULE_PAYMENT_AUTHORIZENET_CC_MD5_HASH . MODULE_PAYMENT_AUTHORIZENET_CC_LOGIN_ID . $inquiry_regs[6] . $inquiry_regs[9]))) {
                $transaction_return_status = '0';
              }
            }
          } else {
            $transaction_return_status = '0';
          }

          $Qtransaction = $osC_Database->query('insert into :table_orders_transactions_history (orders_id, transaction_code, transaction_return_value, transaction_return_status, date_added) values (:orders_id, :transaction_code, :transaction_return_value, :transaction_return_status, now())');
          $Qtransaction->bindTable(':table_orders_transactions_history', TABLE_ORDERS_TRANSACTIONS_HISTORY);
          $Qtransaction->bindInt(':orders_id', $id);
          $Qtransaction->bindInt(':transaction_code', 2);
          $Qtransaction->bindValue(':transaction_return_value', $result);
          $Qtransaction->bindInt(':transaction_return_status', $transaction_return_status);
          $Qtransaction->execute();
        }
      }
    }
  }
?>
