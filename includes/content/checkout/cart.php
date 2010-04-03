<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Checkout_Cart extends osC_Template {
    protected $_module = 'cart';
    protected $_group = 'checkout';
    protected $_page_title;
    protected $_page_contents = 'shopping_cart.php';
    protected $_page_image = 'table_background_cart.gif';

    public function __construct() {
      global $osC_ShoppingCart, $osC_Services, $osC_Breadcrumb;

      if ( isset($_GET['action']) && ($_GET['action'] == 'email') ) {
        $this->_processEmailAddress();
      }

      $this->_page_title = __('shopping_cart_heading');

      if ( !$osC_ShoppingCart->hasContents() ) {
        $this->_page_contents = 'shopping_cart_empty.php';
      }

      if ( $osC_Services->isStarted('breadcrumb') ) {
        $osC_Breadcrumb->add(__('breadcrumb_checkout_shopping_cart'), osc_href_link(FILENAME_CHECKOUT, null, 'SSL'));
      }
    }

    public function requireCustomerAccount() {
      global $osC_ShoppingCart;

      foreach ( $osC_ShoppingCart->getProducts() as $product ) {
        $osC_Product = new osC_Product($product['id']);

        if ( $osC_Product->isTypeActionAllowed(array('perform_order', 'require_customer_account'), null, false) ) {
          return true;
        }
      }

      return false;
    }

    protected function _processEmailAddress() {
      global $osC_Customer, $osC_MessageStack;

      if ( isset($_POST['email']) && (strlen(trim($_POST['email'])) >= ACCOUNT_EMAIL_ADDRESS) ) {
        if ( osc_validate_email_address($_POST['email']) ) {
          $osC_Customer->setEmailAddress(trim($_POST['email']));

          osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'confirmation', 'SSL'));
        } else {
          $osC_MessageStack->add($this->_module, __('field_customer_email_address_check_error'));
        }
      } else {
        $osC_MessageStack->add($this->_module, sprintf(__('field_customer_email_address_error'), ACCOUNT_EMAIL_ADDRESS));
      }
    }
  }
?>
