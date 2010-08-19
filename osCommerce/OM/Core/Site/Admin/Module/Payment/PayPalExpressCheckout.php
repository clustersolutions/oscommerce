<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Module\Payment;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

/**
 * The administration side of the Paypal Express Checkout payment module
 */

  class PayPalExpressCheckout extends \osCommerce\OM\Core\Site\Admin\PaymentModuleAbstract {

/**
 * The administrative title of the payment module
 *
 * @var string
 * @access protected
 */

    protected $_title;

/**
 * The administrative description of the payment module
 *
 * @var string
 * @access protected
 */

    protected $_description;

/**
 * The developers name
 *
 * @var string
 * @access protected
 */

    protected $_author_name = 'osCommerce';

/**
 * The developers address
 *
 * @var string
 * @access protected
 */

    protected $_author_www = 'http://www.oscommerce.com';

/**
 * The status of the module
 *
 * @var boolean
 * @access protected
 */

    protected $_status = false;

/**
 * Initialize module
 *
 * @access protected
 */

    protected function initialize() {
      $this->_title = OSCOM::getDef('paypal_express_checkout_title');
      $this->_description = OSCOM::getDef('paypal_express_checkout_description');
      $this->_status = (defined('MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_STATUS') && (MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_STATUS == '1') ? true : false);
      $this->_sort_order = (defined('MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SORT_ORDER') ? MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SORT_ORDER : 0);
    }

/**
 * Checks to see if the module has been installed
 *
 * @access public
 * @return boolean
 */

    public function isInstalled() {
      return defined('MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_STATUS');
    }

/**
 * Installs the module
 *
 * @access public
 * @see \osCommerce\OM\Core\Site\Admin\PaymentModuleAbstract::install()
 */

    public function install() {
      $OSCOM_Database = Registry::get('Database');

      parent::install();

      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Enable PayPal Express Checkout', 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_STATUS', '-1', 'Do you want to accept PayPal Express Checkout payments?', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Seller Account', 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SELLER_ACCOUNT', '', 'The email address of the seller account if no API credentials has been setup.', '6', '0', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('API Username', 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_USERNAME', '', 'The username to use for the PayPal API service', '6', '0', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('API Password', 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_PASSWORD', '', 'The password to use for the PayPal API service', '6', '0', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('API Signature', 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_SIGNATURE', '', 'The signature to use for the PayPal API service', '6', '0', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Server', 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_SERVER', 'Live', 'Use the live or testing (sandbox) gateway server to process transactions?', '6', '0', 'osc_cfg_set_boolean_value(array(\'Live\', \'Sandbox\'))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Method', 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_METHOD', 'Sale', 'The processing method to use for each transaction.', '6', '0', 'osc_cfg_set_boolean_value(array(\'Authorization\', \'Sale\'))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('PayPal Account Optional', 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ACCOUNT_OPTIONAL', '-1', 'This must also be enabled in your PayPal account, in Profile > Website Payment Preferences.', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('PayPal Instant Update', 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_INSTANT_UPDATE', '1', 'Support PayPal shipping and tax calculations on the PayPal.com site during Express Checkout.', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('PayPal Checkout Image', 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_IMAGE', 'Static', 'Use static or dynamic Express Checkout image buttons. Dynamic images are used with PayPal campaigns.', '6', '0', 'osc_cfg_set_boolean_value(array(\'Static\', \'Dynamic\'))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Debug E-Mail Address', 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_DEBUG_EMAIL', '', 'All parameters of an invalid transaction will be sent to this email address.', '6', '0', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '0', 'osc_cfg_use_get_zone_class_title', 'osc_cfg_set_zone_classes_pull_down_menu', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'osc_cfg_set_order_statuses_pull_down_menu', 'osc_cfg_use_get_order_status_title', now())");
    }

/**
 * Return the configuration parameter keys in an array
 *
 * @access public
 * @return array
 */

    public function getKeys() {
      return array('MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_STATUS',
                   'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SELLER_ACCOUNT',
                   'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_USERNAME',
                   'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_PASSWORD',
                   'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_SIGNATURE',
                   'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_SERVER',
                   'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_METHOD',
                   'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ACCOUNT_OPTIONAL',
                   'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_INSTANT_UPDATE',
                   'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_IMAGE',
                   'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_DEBUG_EMAIL',
                   'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ZONE',
                   'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SORT_ORDER',
                   'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ORDER_STATUS_ID');
    }
  }
?>
