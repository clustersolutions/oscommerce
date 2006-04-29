<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Payment_wirecard_eft extends osC_Payment_Admin {
    var $_title,
        $_code = 'wirecard_eft',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_status = false;

    function osC_Payment_wirecard_eft() {
      global $osC_Language;

      $this->_title = $osC_Language->get('payment_wirecard_eft_title');
      $this->_description = $osC_Language->get('payment_wirecard_eft_description');
      $this->_method_title = $osC_Language->get('payment_wirecard_eft_method_title');
      $this->_status = (defined('MODULE_PAYMENT_WIRECARD_EFT_STATUS') && (MODULE_PAYMENT_WIRECARD_EFT_STATUS == '1') ? true : false);
      $this->_sort_order = (defined('MODULE_PAYMENT_WIRECARD_EFT_SORT_ORDER') ? MODULE_PAYMENT_WIRECARD_EFT_SORT_ORDER : '');

      switch (MODULE_PAYMENT_WIRECARD_EFT_TRANSACTION_SERVER) {
        case 'production':
          $this->_gateway_url = 'https://' . MODULE_PAYMENT_WIRECARD_EFT_USERNAME . ':' . MODULE_PAYMENT_WIRECARD_EFT_PASSWORD . '@frontend-test.wirecard.com/secure/ssl-gateway';
          break;

        default:
          $this->_gateway_url = 'https://' . MODULE_PAYMENT_WIRECARD_EFT_USERNAME . ':' . MODULE_PAYMENT_WIRECARD_EFT_PASSWORD . '@frontend-test.wirecard.com/secure/ssl-gateway';
          break;
      }
    }

    function isInstalled() {
      return defined('MODULE_PAYMENT_WIRECARD_EFT_STATUS');
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Enable Wire Card EFT Module', 'MODULE_PAYMENT_WIRECARD_EFT_STATUS', '-1', 'Do you want to accept Wire Card EFT payments?', '6', '0', 'osc_cfg_get_boolean_value', 'tep_cfg_select_option(array(1, -1), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Gateway Username', 'MODULE_PAYMENT_WIRECARD_EFT_USERNAME', '', 'The username to connect to the gateway with.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Gateway Password', 'MODULE_PAYMENT_WIRECARD_EFT_PASSWORD', '', 'The password to use with the username when connecting to the gateway.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Business Case Signature', 'MODULE_PAYMENT_WIRECARD_EFT_BUSINESS_SIGNATURE', '', 'The Business Case Signature to use when performing transactions.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Server', 'MODULE_PAYMENT_WIRECARD_EFT_TRANSACTION_SERVER', 'test', 'Perform transactions on the production server or on the testing server.', '6', '0', 'tep_cfg_select_option(array(\'production\', \'test\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Mode', 'MODULE_PAYMENT_WIRECARD_EFT_TRANSACTION_MODE', 'test', 'The mode to perform the transactions in.', '6', '0', 'tep_cfg_select_option(array(\'live\', \'demo\', \'test\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_WIRECARD_EFT_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_WIRECARD_EFT_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_WIRECARD_EFT_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0' , now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('MODULE_PAYMENT_WIRECARD_EFT_STATUS',
                             'MODULE_PAYMENT_WIRECARD_EFT_USERNAME',
                             'MODULE_PAYMENT_WIRECARD_EFT_PASSWORD',
                             'MODULE_PAYMENT_WIRECARD_EFT_BUSINESS_SIGNATURE',
                             'MODULE_PAYMENT_WIRECARD_EFT_TRANSACTION_SERVER',
                             'MODULE_PAYMENT_WIRECARD_EFT_TRANSACTION_MODE',
                             'MODULE_PAYMENT_WIRECARD_EFT_ZONE',
                             'MODULE_PAYMENT_WIRECARD_EFT_ORDER_STATUS_ID',
                             'MODULE_PAYMENT_WIRECARD_EFT_SORT_ORDER');
      }

      return $this->_keys;
    }

    function getPostTransactionActions($history) {
      $actions = array();

      if (in_array('2', $history) === false) {
        $actions[2] = 'cancelTransaction';
      }

      return $actions;
    }

    function cancelTransaction($id) {
      global $osC_Database;

      $Qorder = $osC_Database->query('select transaction_return_value from :table_orders_transactions_history where orders_id = :orders_id and (transaction_code = 1 or transaction_code = 3) order by date_added desc limit 1');
      $Qorder->bindTable(':table_orders_transactions_history', TABLE_ORDERS_TRANSACTIONS_HISTORY);
      $Qorder->bindInt(':orders_id', $id);
      $Qorder->execute();

      if ($Qorder->numberOfRows() === 1) {
        $osC_XML = new osC_XML($Qorder->value('transaction_return_value'));
        $result_array = $osC_XML->toArray();
        $result_array = array_shift($result_array);

        $post_string = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
                       '<WIRECARD_BXML xmlns:xsi="http://www.w3.org/1999/XMLSchema-instance" xsi:noNamespaceSchemaLocation="wirecard.xsd">' . "\n" .
                       '  <W_REQUEST>' . "\n" .
                       '    <W_JOB>' . "\n" .
                       '      <JobID>Job 1</JobID>' . "\n" .
                       '      <BusinessCaseSignature>' . MODULE_PAYMENT_WIRECARD_EFT_BUSINESS_SIGNATURE . '</BusinessCaseSignature>' . "\n" .
                       '      <FNC_FT_CANCEL>' . "\n" .
                       '        <FunctionID>Reversal 1</FunctionID>' . "\n" .
                       '        <FT_TRANSACTION mode="' . MODULE_PAYMENT_WIRECARD_EFT_TRANSACTION_MODE . '">' . "\n" .
                       '          <TransactionID>' . $result_array['W_JOB']['FNC_FT_DEBIT']['FT_TRANSACTION']['TransactionID'] . '</TransactionID>' . "\n" .
                       '          <ReferenceGuWID>' . $result_array['W_JOB']['FNC_FT_DEBIT']['FT_TRANSACTION']['PROCESSING_STATUS']['GuWID'] . '</ReferenceGuWID>' . "\n" .
                       '        </FT_TRANSACTION>' . "\n" .
                       '      </FNC_FT_CANCEL>' . "\n" .
                       '    </W_JOB>' . "\n" .
                       '  </W_REQUEST>' . "\n" .
                       '</WIRECARD_BXML>';

        $result = osC_Payment::sendTransactionToGateway($this->_gateway_url, $post_string, array('Content-type: text/xml'));

        if (empty($result) === false) {
          $osC_XML = new osC_XML($result);
          $result_array = $osC_XML->toArray();
          $result_array = array_shift($result_array);

          $transaction_return_status = '0';

          if (isset($result_array['W_JOB']['FNC_FT_CANCEL']['FT_TRANSACTION']['PROCESSING_STATUS']['FunctionResult'])) {
            if ($result_array['W_JOB']['FNC_FT_CANCEL']['FT_TRANSACTION']['PROCESSING_STATUS']['FunctionResult'] == 'ACK') {
              $transaction_return_status = '1';
            }
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
