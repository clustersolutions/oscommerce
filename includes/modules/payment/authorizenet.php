<?php
/*
  $Id: authorizenet.php,v 1.54 2004/07/22 21:57:55 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class authorizenet {
    var $code, $title, $description, $sort_order, $enabled = false, $order_status, $form_action_url;
    var $cc_card_type, $cc_card_number, $cc_expiry_month, $cc_expiry_year;

    function authorizenet() {
      $this->code = 'authorizenet';
      $this->title = MODULE_PAYMENT_AUTHORIZENET_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_AUTHORIZENET_TEXT_DESCRIPTION;

      if (defined('MODULE_PAYMENT_AUTHORIZENET_STATUS')) {
        $this->initialize();
      }
    }

    function initialize() {
      global $order;

// need if (exists()) and error message maybe have admin message too
      if (file_exists(MODULE_PAYMENT_AUTHORIZENET_LOGIN_FILE)) {
        include(MODULE_PAYMENT_AUTHORIZENET_LOGIN_FILE); // Get our private defines
      }

      $this->enabled = ((MODULE_PAYMENT_AUTHORIZENET_STATUS == 'True') ? true : false);
      if (MODULE_PAYMENT_AUTHORIZENET_LOGIN == 'MODULE_PAYMENT_AUTHORIZENET_LOGIN') {
        $this->enabled = false; // Login Name not defined, disable
      }
      $this->sort_order = MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER;

      if ((int)MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      if (MODULE_PAYMENT_AUTHORIZENET_GATEWAY_METHOD == 'SIM') {
        $this->form_action_url = 'https://secure.authorize.net/gateway/transact.dll';
      // } else $this->process_action_url = 'https://certification.authorize.net/gateway/transact.dll';
      } else $this->process_action_url = 'https://secure.authorize.net/gateway/transact.dll';
    }

// DISCLAIMER:
//     This code is distributed in the hope that it will be useful, but without any warranty;
//     without even the implied warranty of merchantability or fitness for a particular purpose.

// Main Interfaces:
//
// function InsertFP ($loginid, $txnkey, $amount, $sequence) - Insert HTML form elements required for SIM
// function CalculateFP ($loginid, $txnkey, $amount, $sequence, $tstamp) - Returns Fingerprint.

// compute HMAC-MD5
// Uses PHP mhash extension. Pl sure to enable the extension
// function hmac ($key, $data) {
//   return (bin2hex (mhash(MHASH_MD5, $data, $key)));
//}

// Thanks is lance from http://www.php.net/manual/en/function.mhash.php
//lance_rushing at hot* spamfree *mail dot com
//27-Nov-2002 09:36
//
//Want to Create a md5 HMAC, but don't have hmash installed?
//
//Use this:

function hmac ($key, $data)
{
   // RFC 2104 HMAC implementation for php.
   // Creates an md5 HMAC.
   // Eliminates the need to install mhash to compute a HMAC
   // Hacked by Lance Rushing

   $b = 64; // byte length for md5
   if (strlen($key) > $b) {
       $key = pack("H*",md5($key));
   }
   $key  = str_pad($key, $b, chr(0x00));
   $ipad = str_pad('', $b, chr(0x36));
   $opad = str_pad('', $b, chr(0x5c));
   $k_ipad = $key ^ $ipad ;
   $k_opad = $key ^ $opad;

   return md5($k_opad  . pack("H*",md5($k_ipad . $data)));
}
// end code from lance (resume authorize.net code)

// Calculate and return fingerprint
// Use when you need control on the HTML output
function CalculateFP ($loginid, $txnkey, $amount, $sequence, $tstamp, $currency = "") {
  return ($this->hmac ($txnkey, $loginid . "^" . $sequence . "^" . $tstamp . "^" . $amount . "^" . $currency));
}

// Inserts the hidden variables in the HTML FORM required for SIM
// Invokes hmac function to calculate fingerprint.

function InsertFP ($loginid, $txnkey, $amount, $sequence, $currency = "") {
  $tstamp = time ();
  $fingerprint = $this->hmac ($txnkey, $loginid . "^" . $sequence . "^" . $tstamp . "^" . $amount . "^" . $currency);

  $vars = array('x_fp_sequence' => $sequence,
                'x_fp_timestamp' => $tstamp,
                'x_fp_hash' => $fingerprint);

  return $vars;
}
// end authorize.net code

    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_AUTHORIZENET_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_AUTHORIZENET_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
      $js = '  if (payment_value == "' . $this->code . '") {' . "\n" .
            '    var cc_owner = document.checkout_payment.authorizenet_cc_owner.value;' . "\n" .
            '    var cc_number = document.checkout_payment.authorizenet_cc_number.value;' . "\n" .
            '    if (cc_owner == "" || cc_owner.length < ' . CC_OWNER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_AUTHORIZENET_TEXT_JS_CC_OWNER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '    if (cc_number == "" || cc_number.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_AUTHORIZENET_TEXT_JS_CC_NUMBER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '  }' . "\n";

      return $js;
    }

    function selection() {
      global $order;

      if (MODULE_PAYMENT_AUTHORIZENET_METHOD == 'Credit Card') {
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
                           'fields' => array(array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_OWNER,
                                                   'field' => osc_draw_input_field('authorizenet_cc_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'])),
                                             array('title' => MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_TYPE,
                                                   'field' => osc_draw_pull_down_menu('authorizenet_cc_type', $credit_cards)),
                                             array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_NUMBER,
                                                   'field' => osc_draw_input_field('authorizenet_cc_number')),
                                             array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_EXPIRES,
                                                   'field' => osc_draw_pull_down_menu('authorizenet_cc_expires_month', $expires_month, $today['mon']) . '&nbsp;' . osc_draw_pull_down_menu('authorizenet_cc_expires_year', $expires_year))));
      } else { // eCheck
        $acct_types = array(array('id' => 'CHECKING', 'text' => MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_TYPE_CHECK),
                            array('id' => 'SAVINGS', 'text' => MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_TYPE_SAVINGS));

        $fields = array(array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_NAME,
                              'field' => osc_draw_input_field('authorizenet_bank_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'])),
                        array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_TYPE,
                              'field' => osc_draw_pull_down_menu('authorizenet_bank_acct_type', $acct_types)),
                        array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_NAME,
                              'field' => osc_draw_input_field('authorizenet_bank_name')),
                        array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ABA_CODE,
                              'field' => osc_draw_input_field('authorizenet_bank_aba')),
                        array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_NUM,
                              'field' => osc_draw_input_field('authorizenet_bank_acct')));

        if (MODULE_PAYMENT_AUTHORIZENET_WELLSFARGO == 'Yes') { // Add extra fields
          $org_types = array(array('id' => 'I', 'text' => MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_ORG_PERSONAL),
                             array('id' => 'B', 'text' => MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_ORG_BUSINESS));

          $fields_wf = array(array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_ORG,
                                   'field' => osc_draw_pull_down_menu('wellsfargo_org_type', $org_types)),
                             array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_INTRO,
                                   'field' => ''),
                             array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_TAXID,
                                   'field' => osc_draw_input_field('wellsfargo_taxid')),
                             array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_DLNUM,
                                   'field' => osc_draw_input_field('wellsfargo_dlnum')),
                             array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_STATE,
                                   'field' => osc_draw_input_field('wellsfargo_state')),
                             array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_DOB,
                                   'field' => osc_draw_input_field('wellsfargo_dob')));
           $fields = array_merge($fields, $fields_wf);
        }
        $selection = array('id' => $this->code,
                           'module' => $this->title,
                           'fields' => $fields);
      }
      return $selection;
    }


    function pre_confirmation_check() {
      global $messageStack;

      if (PHP_VERSION < 4.1) {
        global $_POST;
      }

      if (MODULE_PAYMENT_AUTHORIZENET_METHOD == 'Credit Card') {
        if (!tep_validate_credit_card($_POST['ipayment_cc_number'])) {
          $messageStack->add_session('checkout_payment', TEXT_CCVAL_ERROR_INVALID_NUMBER, 'error');

          $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) . '&authorizenet_cc_owner=' . urlencode($_POST['authorizenet_cc_owner']) . '&authorizenet_cc_expires_month=' . $_POST['authorizenet_cc_expires_month'] . '&authorizenet_cc_expires_year=' . $_POST['authorizenet_cc_expires_year'];

        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL'));
      }

        $this->cc_card_owner = $_POST['ipayment_cc_owner'];
        $this->cc_card_type = $_POST['authorizenet_cc_type'];
        $this->cc_card_number = $_POST['authorizenet_cc_number'];
        $this->cc_expiry_month = $_POST['authorizenet_cc_expires_month'];
        $this->cc_expiry_year = $_POST['authorizenet_cc_expires_year'];
      }
    }

    function confirmation() {
      if (PHP_VERSION < 4.1) {
        global $_POST;
      }

      if (MODULE_PAYMENT_AUTHORIZENET_METHOD == 'Credit Card') {
        $confirmation = array('title' => $this->title . ': ' . $this->cc_card_type,
                              'fields' => array(array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_OWNER,
                                                      'field' => $this->cc_card_owner),
                                                array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_NUMBER,
                                                      'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4)),
                                                array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_EXPIRES,
                                                      'field' => strftime('%B, %Y', mktime(0,0,0,$this->cc_expiry_month, 1, '20' . $this->cc_expiry_year)))));
      } else { // eCheck
        $fields = array(array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_NAME,
                              'field' => $_POST['authorizenet_bank_owner']),
                        array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_TYPE,
                              'field' => $_POST['authorizenet_bank_acct_type']),
                        array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_NAME,
                              'field' => $_POST['authorizenet_bank_name']),
                        array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ABA_CODE,
                              'field' => $_POST['authorizenet_bank_aba']),
                        array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_NUM,
                              'field' => $_POST['authorizenet_bank_acct']));

        if (MODULE_PAYMENT_AUTHORIZENET_WELLSFARGO == 'Yes') { // Add extra fields
          if (tep_not_null($_POST['wellsfargo_taxid'])) {
            $fields_wf = array(array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_TAXID,
                                     'field' => $_POST['wellsfargo_taxid']));
          } else {
            $fields_wf = array(array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_DLNUM,
                                     'field' => $_POST['wellsfargo_dlnum']),
                               array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_STATE,
                                     'field' => $_POST['wellsfargo_state']),
                               array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_DOB,
                                     'field' => $_POST['wellsfargo_dob']));
          }
          $fields = array_merge($fields, $fields_wf);
        }
        $confirmation = array('title' => $this->title . ' : eCheck',
                              'fields' => $fields);
      }
      return $confirmation;
    }

    function make_gateway_vars() {
      global $order, $osC_Customer;

      if (MODULE_PAYMENT_AUTHORIZENET_METHOD == 'Credit Card') {
        $gw_pay_type = array('x_Card_Num' => $this->cc_card_number,
                             'x_Exp_Date' => $this->cc_expiry_month . substr($this->cc_expiry_year, -2),
                             'x_Type' => MODULE_PAYMENT_AUTHORIZENET_CREDIT_CAPTURE,
                             'x_Method' => 'CC');
      }
      if (MODULE_PAYMENT_AUTHORIZENET_METHOD == 'eCheck') {
        $gw_pay_type = array('x_bank_acct_name' => $this->ec_bank_owner,
                             'x_bank_acct_type' => $this->ec_bank_acct_type,
                             'x_bank_name' => $this->ec_bank_name,
                             'x_bank_aba_code' => $this->ec_bank_aba,
                             'x_bank_acct_num' => $this->ec_bank_acct,
                             'x_Type' => 'AUTH_CAPTURE',
                             'x_echeck_type' => 'WEB',
                             'x_Method' => 'ECHECK');

        if (MODULE_PAYMENT_AUTHORIZENET_WELLSFARGO == 'Yes') { // Add extra fields
          if (tep_not_null($this->wf_taxid)) {
            $gw_pay_type2 = array('x_customer_tax_id' => $this->wf_taxid,
                                  'x_customer_organization_type' => $this->wf_org_type);
          } else {
            $gw_pay_type2 = array('x_drivers_license_number' => $this->wf_dlnum,
                                  'x_drivers_license_state' => $this->wf_state,
                                  'x_drivers_license_dob' => $this->wf_dob,
                                  'x_customer_organization_type' => $this->wf_org_type);
          }
          $gw_pay_type = array_merge($gw_pay_type, $gw_pay_type2);
        }
      }
      $gw_common= array('x_Login' => MODULE_PAYMENT_AUTHORIZENET_LOGIN,
                        'x_tran_key' => MODULE_PAYMENT_AUTHORIZENET_TXNKEY,
                        'x_Amount' => number_format($order->info['total'], 2, '.', ''),
                        'x_Version' => '3.0',
                        'x_Cust_ID' => $osC_Customer,
                        'x_Email_Customer' => ((MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER == 'True') ? 'TRUE': 'FALSE'),
                        'x_first_name' => $order->billing['firstname'],
                        'x_last_name' => $order->billing['lastname'],
                        'x_company' => $order->billing['company'],
                        'x_address' => $order->billing['street_address'],
                        'x_city' => $order->billing['city'],
                        'x_state' => $order->billing['state'],
                        'x_zip' => $order->billing['postcode'],
                        'x_country' => $order->billing['country']['title'],
                        'x_phone' => $order->customer['telephone'],
                        'x_email' => $order->customer['email_address'],
                        'x_ship_to_first_name' => $order->delivery['firstname'],
                        'x_ship_to_last_name' => $order->delivery['lastname'],
                        'x_ship_to_address' => $order->delivery['street_address'],
                        'x_ship_to_city' => $order->delivery['city'],
                        'x_ship_to_state' => $order->delivery['state'],
                        'x_ship_to_zip' => $order->delivery['postcode'],
                        'x_ship_to_country' => $order->delivery['country']['title'],
                        'x_Customer_IP' => tep_get_ip_address());

      $gw_vars = array_merge($gw_common, $gw_pay_type);
      return $gw_vars;
    }

    function process_button() {
      if (PHP_VERSION < 4.1) {
        global $_POST;
      }

      $process_button_string = '';

      if (MODULE_PAYMENT_AUTHORIZENET_GATEWAY_METHOD == 'SIM') {
        $gw_vars = $this->make_gateway_vars();
        $sequence = rand(1, 1000);
        $gw_vars = array_merge($gw_vars, $this->InsertFP(MODULE_PAYMENT_AUTHORIZENET_LOGIN, MODULE_PAYMENT_AUTHORIZENET_TXNKEY, $gw_vars['X_Amount'], $sequence));
        $gw_vars['x_Relay_URL'] = tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL', false);
        $gw_vars['x_Relay_Response'] = 'TRUE';
        $gw_vars['x_delim_data'] = 'TRUE';
        $gw_vars['x_delim_char'] = '|';
        if (MODULE_PAYMENT_AUTHORIZENET_TESTMODE == 'Test') $gw_vars['x_Test_Request'] = 'TRUE';
        $gw_vars[$osC_Session->name] = $osC_Session->id;
        reset($gw_vars);
        while (list($key, $value) = each($gw_vars)) {
          $process_button_string .= osc_draw_hidden_field($key, $value) . "\n";
        }
      } else {
        if (MODULE_PAYMENT_AUTHORIZENET_METHOD == 'Credit Card') {
          $process_button_string .= osc_draw_hidden_field('authorizenet_cc_number', $this->cc_card_number) . "\n";
          $process_button_string .= osc_draw_hidden_field('authorizenet_cc_expiry_month', $this->cc_expiry_month) . "\n";
          $process_button_string .= osc_draw_hidden_field('authorizenet_cc_expiry_year', $this->cc_expiry_year) . "\n";
        } else { // eCheck
          $process_button_string .= osc_draw_hidden_field('authorizenet_bank_owner', $_POST['authorizenet_bank_owner']) . "\n";
          $process_button_string .= osc_draw_hidden_field('authorizenet_bank_acct_type', $_POST['authorizenet_bank_acct_type']) . "\n";
          $process_button_string .= osc_draw_hidden_field('authorizenet_bank_name', $_POST['authorizenet_bank_name']) . "\n";
          $process_button_string .= osc_draw_hidden_field('authorizenet_bank_aba', $_POST['authorizenet_bank_aba']) . "\n";
          $process_button_string .= osc_draw_hidden_field('authorizenet_bank_acct', $_POST['authorizenet_bank_acct']) . "\n";
          if (MODULE_PAYMENT_AUTHORIZENET_WELLSFARGO == 'Yes') { // Add extra fields
            $process_button_string .= osc_draw_hidden_field('wellsfargo_taxid', $_POST['wellsfargo_taxid']) . "\n";
            $process_button_string .= osc_draw_hidden_field('wellsfargo_dlnum', $_POST['wellsfargo_dlnum']) . "\n";
            $process_button_string .= osc_draw_hidden_field('wellsfargo_state', $_POST['wellsfargo_state']) . "\n";
            $process_button_string .= osc_draw_hidden_field('wellsfargo_dob', $_POST['wellsfargo_dob']) . "\n";
            $process_button_string .= osc_draw_hidden_field('wellsfargo_org_type', $_POST['wellsfargo_org_type']) . "\n";
          }
        }
      }
      return $process_button_string;
    }

    function before_process() {
      if (PHP_VERSION < 4.1) {
        global $_POST;
      }

      if (MODULE_PAYMENT_AUTHORIZENET_GATEWAY_METHOD == 'AIM') {
        if (MODULE_PAYMENT_AUTHORIZENET_METHOD == 'Credit Card') {
          $this->cc_card_number = $_POST['authorizenet_cc_number'];
          $this->cc_expiry_month = $_POST['authorizenet_cc_expiry_month'];
          $this->cc_expiry_year = $_POST['authorizenet_cc_expiry_year'];
        } else { // eCheck
          $this->ec_bank_owner = $_POST['authorizenet_bank_owner'];
          $this->ec_bank_acct_type = $_POST['authorizenet_bank_acct_type'];
          $this->ec_bank_name = $_POST['authorizenet_bank_name'];
          $this->ec_bank_aba = $_POST['authorizenet_bank_aba'];
          $this->ec_bank_acct = $_POST['authorizenet_bank_acct'];
          if (MODULE_PAYMENT_AUTHORIZENET_WELLSFARGO == 'Yes') { // Add extra fields
            $this->wf_taxid = $_POST['wellsfargo_taxid'];
            $this->wf_dlnum = $_POST['wellsfargo_dlnum'];
            $this->wf_state = $_POST['wellsfargo_state'];
            $this->wf_dob = $_POST['wellsfargo_dob'];
            $this->wf_org_type = $_POST['wellsfargo_org_type'];
          }
        }
        $gw_vars = $this->make_gateway_vars();
        $sequence = rand(1, 1000);
        // $gw_vars = array_merge($gw_vars, $this->InsertFP(MODULE_PAYMENT_AUTHORIZENET_LOGIN, MODULE_PAYMENT_AUTHORIZENET_TXNKEY, $gw_vars['X_Amount'], $sequence));
        if (MODULE_PAYMENT_AUTHORIZENET_TESTMODE == 'Test') $gw_vars['x_Test_Request'] = 'TRUE';
        $gw_vars[$osC_Session->name] = $osC_Session->id;
        $gw_vars['x_delim_data'] = 'TRUE';
        $gw_vars['x_delim_char'] = '|';
        $gw_vars['x_relay_response'] = 'FALSE';
        reset($gw_vars);
        $curl_opts = ' ' . $this->process_action_url;
        while (list($key, $value) = each($gw_vars)) {
          $curl_opts .= " -d " . $key . "=" . urlencode($value);
        }
        $handle = popen(MODULE_PAYMENT_AUTHORIZENET_CURL . $curl_opts, "r");
        $str = '';
        while (!feof($handle)) {
          $str .= fread($handle, 2048);
        }
        pclose($handle);
        $result = explode("|", urldecode($str));
        $x_response_code = $result[0];
        $x_response_reason_text = $result[3];
// echo $x_response_code . " " . $x_response_reason_text . "<br>";
 // echo urldecode($str); exit(0);
      } else {
        $x_response_code = $_POST['x_response_code'];
        $x_response_reason_text = $_POST['x_response_reason_text'];
      }

      if ($x_response_code == '1') return;
      if ($x_response_code == '2') {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(MODULE_PAYMENT_AUTHORIZENET_TEXT_DECLINED_MESSAGE.$x_response_reason_text), 'SSL', true, false));
      }
      // Code 3 is an error - but anything else is an error too (IMHO)
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(MODULE_PAYMENT_AUTHORIZENET_TEXT_ERROR_MESSAGE.$x_response_reason_text) . '&error=' . urlencode($x_response_reason_text), 'SSL', true, false));
    }

    function after_process() {
      return false;
    }

    function get_error() {
      if (PHP_VERSION < 4.1) {
        global $_GET;
      }

      $error = array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_ERROR,
                     'error' => urldecode($_GET['error']));

      return $error;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_AUTHORIZENET_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Authorize.net Module', 'MODULE_PAYMENT_AUTHORIZENET_STATUS', 'True', 'Do you want to accept Authorize.net payments?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Include file that defines the Login Username', 'MODULE_PAYMENT_AUTHORIZENET_LOGIN_FILE', '" . DIR_FS_CATALOG . "includes/local/auth_login.php', 'The full path to the file that defines the login username used for the Authorize.net service. This should be a secure file. The PHP variable MODULE_PAYMENT_AUTHORIZENET_LOGIN must be defined here.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Key', 'MODULE_PAYMENT_AUTHORIZENET_TXNKEY', 'Test', 'Transaction Key used for encrypting TP data', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Mode', 'MODULE_PAYMENT_AUTHORIZENET_TESTMODE', 'Test', 'Transaction mode used for processing orders', '6', '0', 'tep_cfg_select_option(array(\'Test\', \'Production\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Method', 'MODULE_PAYMENT_AUTHORIZENET_METHOD', 'Credit Card', 'Transaction method used for processing orders', '6', '0', 'tep_cfg_select_option(array(\'Credit Card\', \'eCheck\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Gateway Method', 'MODULE_PAYMENT_AUTHORIZENET_GATEWAY_METHOD', 'AIM', 'Gateway transaction method used for processing orders', '6', '0', 'tep_cfg_select_option(array(\'AIM\', \'SIM\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Customer Notifications', 'MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER', 'False', 'Should Authorize.Net e-mail a receipt to the customer?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_AUTHORIZENET_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Credit Card Mode', 'MODULE_PAYMENT_AUTHORIZENET_CREDIT_CAPTURE', 'AUTH_CAPTURE', 'Credit Card processing method. Authorize Only or Authorize and Capture (Collect Funds)', '6', '0', 'tep_cfg_select_option(array(\'AUTH_CAPTURE\', \'AUTH_ONLY\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Wells Fargo Secure Source Account?', 'MODULE_PAYMENT_AUTHORIZENET_WELLSFARGO', 'No', 'Set to YES if your account is with Wells Fargo', '6', '0', 'tep_cfg_select_option(array(\'No\', \'Yes\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Path to cURL', 'MODULE_PAYMENT_AUTHORIZENET_CURL', '/usr/local/bin/curl', 'The full path to the cURL program (ask your hosting provider)', '6', '0', now())");
    }

// Authorize.net utility functions
    function keys() {
      return array('MODULE_PAYMENT_AUTHORIZENET_STATUS', 'MODULE_PAYMENT_AUTHORIZENET_LOGIN_FILE', 'MODULE_PAYMENT_AUTHORIZENET_TXNKEY', 'MODULE_PAYMENT_AUTHORIZENET_GATEWAY_METHOD', 'MODULE_PAYMENT_AUTHORIZENET_TESTMODE', 'MODULE_PAYMENT_AUTHORIZENET_METHOD', 'MODULE_PAYMENT_AUTHORIZENET_CREDIT_CAPTURE', 'MODULE_PAYMENT_AUTHORIZENET_WELLSFARGO', 'MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER', 'MODULE_PAYMENT_AUTHORIZENET_ZONE', 'MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID', 'MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER', 'MODULE_PAYMENT_AUTHORIZENET_CURL');
    }
  }
?>
