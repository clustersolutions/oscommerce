<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Application\Checkout;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Product;
  use osCommerce\OM\Core\Site\Shop\Payment;

  class Controller extends \osCommerce\OM\Core\Site\Shop\ApplicationAbstract {
    protected function initialize() {}

    protected function process() {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
      $OSCOM_MessageStack = Registry::get('MessageStack');

// redirect to shopping cart if shopping cart is empty
      if ( !$OSCOM_ShoppingCart->hasContents() ) {
        osc_redirect(OSCOM::getLink(null, 'Cart'));
      }

// check for e-mail address
      if ( !$OSCOM_Customer->hasEmailAddress() ) {
        if ( isset($_POST['email']) && (strlen(trim($_POST['email'])) >= ACCOUNT_EMAIL_ADDRESS) ) {
          if ( osc_validate_email_address($_POST['email']) ) {
            $OSCOM_Customer->setEmailAddress(trim($_POST['email']));
          } else {
            $OSCOM_MessageStack->add('Cart', OSCOM::getDef('field_customer_email_address_check_error'));

            osc_redirect(OSCOM::getLink(null, 'Cart'));
          }
        } else {
          $OSCOM_MessageStack->add('Cart', sprintf(OSCOM::getDef('field_customer_email_address_error'), ACCOUNT_EMAIL_ADDRESS));

          osc_redirect(OSCOM::getLink(null, 'Cart'));
        }
      }

// check product type perform_order conditions
      foreach ( $OSCOM_ShoppingCart->getProducts() as $product ) {
        $OSCOM_Product = new Product($product['id']);
        $OSCOM_Product->isTypeActionAllowed('PerformOrder');
      }

      $OSCOM_Language->load('checkout');
      $OSCOM_Language->load('order');

      $this->_page_title = OSCOM::getDef('confirmation_heading');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_checkout_confirmation'), OSCOM::getLink(null, 'Checkout', null, 'SSL'));
      }

      if ( isset($_POST['comments']) && isset($_SESSION['comments']) && empty($_POST['comments']) ) {
        unset($_SESSION['comments']);
      } elseif ( !empty($_POST['comments']) ) {
        $_SESSION['comments'] = osc_sanitize_string($_POST['comments']);
      }

      if ( DISPLAY_CONDITIONS_ON_CHECKOUT == '1' ) {
        if ( !isset($_POST['conditions']) || ($_POST['conditions'] != '1') ) {
          $OSCOM_MessageStack->add('Checkout', OSCOM::getDef('error_conditions_not_accepted'), 'error');
        }
      }

      if ( Registry::exists('Payment') === false ) {
        Registry::set('Payment', new Payment());
      }

      if ( $OSCOM_ShoppingCart->hasBillingMethod() ) {
        $OSCOM_Payment = Registry::get('Payment');
        $OSCOM_Payment->load($OSCOM_ShoppingCart->getBillingMethod('id'));
      }
    }
  }
?>
