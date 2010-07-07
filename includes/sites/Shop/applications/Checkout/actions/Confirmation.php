<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Checkout\Action;

  use osCommerce\OM\ApplicationAbstract;
  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Site\Shop\Product;
  use osCommerce\OM\Site\Shop\Payment;

  class Confirmation {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
      $OSCOM_MessageStack = Registry::get('MessageStack');

// redirect to shopping cart if shopping cart is empty
      if ( !$OSCOM_ShoppingCart->hasContents() ) {
        osc_redirect(OSCOM::getLink(null, null, null, 'SSL'));
      }

// check product type perform_order conditions
      foreach ( $OSCOM_ShoppingCart->getProducts() as $product ) {
        $OSCOM_Product = new Product($product['id']);

        if ( !$OSCOM_Product->isTypeActionAllowed('PerformOrder') ) {
          osc_redirect(OSCOM::getLink(null, null, null, 'SSL'));
        }
      }

      if ( $OSCOM_ShoppingCart->hasBillingMethod() === false ) {
        Registry::set('Payment', new Payment());
        $OSCOM_Payment = Registry::get('Payment');

        $payment_modules = $OSCOM_Payment->getActive();
        $payment_module = $payment_modules[0];

        $OSCOM_PaymentModule = Registry::get('Payment_' . $payment_module);

        $OSCOM_ShoppingCart->setBillingMethod(array('id' => $OSCOM_PaymentModule->getCode(),
                                                    'title' => $OSCOM_PaymentModule->getMethodTitle()));
      } else {
        Registry::set('Payment', new Payment($OSCOM_ShoppingCart->getBillingMethod('id')));
        $OSCOM_Payment = Registry::get('Payment');
      }

//      if ( isset($_GET[$this->_module]) && ($_GET[$this->_module] == 'process') ) {
//        $osC_Payment->process();

//        $osC_ShoppingCart->reset(true);

// unregister session variables used during checkout
//        unset($_SESSION['comments']);

//        osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'success', 'SSL'));
//      }

      $application->setPageTitle(OSCOM::getDef('confirmation_heading'));
      $application->setPageContent('confirmation.php');

      $OSCOM_Language->load('order');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_checkout_confirmation'), OSCOM::getLink(null, 'Checkout', 'Confirmation', 'SSL'));
      }

      if ( isset($_POST['comments']) && isset($_SESSION['comments']) && empty($_POST['comments']) ) {
        unset($_SESSION['comments']);
      } elseif ( !empty($_POST['comments']) ) {
        $_SESSION['comments'] = osc_sanitize_string($_POST['comments']);
      }

      if ( DISPLAY_CONDITIONS_ON_CHECKOUT == '1' ) {
        if ( !isset($_POST['conditions']) || ($_POST['conditions'] != '1') ) {
          $OSCOM_MessageStack->add('CheckoutPayment', OSCOM::getDef('error_conditions_not_accepted'), 'error');
        }
      }

      if ( $OSCOM_Payment->hasActive() ) {
        $OSCOM_Payment->pre_confirmation_check();
      }
    }
  }
?>
