<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/classes/address_book.php');
  require('includes/classes/payment.php');
  require('includes/classes/order.php');

  class osC_Checkout_Confirmation extends osC_Template {
    protected $_module = 'confirmation';
    protected $_group = 'checkout';
    protected $_page_title;
    protected $_page_contents = 'confirmation.php';
    protected $_page_image = 'table_background_confirmation.gif';

    public function __construct() {
      global $osC_Services, $osC_Language, $osC_ShoppingCart, $osC_MessageStack, $osC_NavigationHistory, $osC_Breadcrumb, $osC_Payment;

// redirect to shopping cart if shopping cart is empty
      if ( !$osC_ShoppingCart->hasContents() ) {
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'));
      }

// check product type perform_order conditions
      foreach ( $osC_ShoppingCart->getProducts() as $product ) {
        $osC_Product = new osC_Product($product['id']);

        if ( !$osC_Product->isTypeActionAllowed('perform_order') ) {
          osc_redirect(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'));
        }
      }

// load the selected payment module
      $osC_Payment = new osC_Payment($osC_ShoppingCart->getBillingMethod('id'));

      if ( isset($_GET[$this->_module]) && ($_GET[$this->_module] == 'process') ) {
        $osC_Payment->process();

        $osC_ShoppingCart->reset(true);

// unregister session variables used during checkout
        unset($_SESSION['comments']);

        osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'success', 'SSL'));
      }

      $this->_page_title = __('confirmation_heading');

      $osC_Language->load('order');

      if ( $osC_Services->isStarted('breadcrumb') ) {
        $osC_Breadcrumb->add(__('breadcrumb_checkout_confirmation'), osc_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

      if ( (isset($_POST['comments'])) && (isset($_SESSION['comments'])) && (empty($_POST['comments'])) ) {
        unset($_SESSION['comments']);
      } elseif (!empty($_POST['comments'])) {
        $_SESSION['comments'] = osc_sanitize_string($_POST['comments']);
      }

      if (DISPLAY_CONDITIONS_ON_CHECKOUT == '1') {
        if (!isset($_POST['conditions']) || ($_POST['conditions'] != '1')) {
          $osC_MessageStack->add('checkout_payment', __('error_conditions_not_accepted'), 'error');
        }
      }

      if ($osC_Payment->hasActive()) {
        $osC_Payment->pre_confirmation_check();
      }
    }
  }
?>
