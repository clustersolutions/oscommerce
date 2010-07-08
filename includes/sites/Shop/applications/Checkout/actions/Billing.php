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
  use osCommerce\OM\ObjectInfo;
  use osCommerce\OM\Site\Shop\Product;
  use osCommerce\OM\Site\Shop\Payment;

  class Billing {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
      $OSCOM_Template = Registry::get('Template');

      global $osC_oiAddress; // HPDL

// redirect to shopping cart if shopping cart is empty
      if ( !$OSCOM_ShoppingCart->hasContents() ) {
        osc_redirect(OSCOM::getLink(null, null, null, 'SSL'));
      }

// check product type perform_order conditions
      foreach ( $OSCOM_ShoppingCart->getProducts() as $product ) {
        $OSCOM_Product = new Product($product['id']);

        if ( !$OSCOM_Product->isTypeActionAllowed('PerformOrder', 'RequireBilling') ) {
          osc_redirect(OSCOM::getLink(null, null, null, 'SSL'));
        }
      }

      $application->setPageTitle(OSCOM::getDef('payment_method_heading'));
      $application->setPageContent('billing.php');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_checkout_payment'), OSCOM::getLink(null, null, 'Billing', 'SSL'));
      }

// load billing address page if no default address exists
      if ( !$OSCOM_ShoppingCart->hasBillingAddress() ) {
        $application->setPageTitle(OSCOM::getDef('payment_address_heading'));
        $application->setPageContent('billing_address.php');

        $OSCOM_Template->addJavascriptFilename('templates/' . $OSCOM_Template->getCode() . '/javascript/checkout_payment_address.js');
        $OSCOM_Template->addJavascriptPhpFilename('includes/form_check.js.php');

        if ( !$OSCOM_Customer->isLoggedOn() ) {
          $osC_oiAddress = new ObjectInfo($OSCOM_ShoppingCart->getBillingAddress());
        }
      } else {
        $OSCOM_Template->addJavascriptFilename('templates/' . $OSCOM_Template->getCode() . '/javascript/checkout_payment.js');

// load all enabled payment modules
        Registry::set('Payment', new Payment());
        $OSCOM_Payment = Registry::get('Payment');

        $OSCOM_Template->addJavascriptBlock($OSCOM_Payment->getJavascriptBlocks());
      }

// HPDL
//      if (isset($_GET['payment_error']) && is_object(${$_GET['payment_error']}) && ($error = ${$_GET['payment_error']}->get_error())) {
//        $OSCOM_MessageStack->add('CheckoutBilling', $error['error'], 'error');
//      }
    }
  }
?>
