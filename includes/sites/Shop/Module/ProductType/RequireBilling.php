<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\ProductType;

  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Site\Shop\Product;
  use osCommerce\OM\Site\Shop\Payment;

  class RequireBilling {
    public static function getTitle() {
      return 'Require Billing';
    }

    public static function getDescription() {
      return 'Require billing';
    }

    public static function isValid(Product $OSCOM_Product) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      if ( $OSCOM_ShoppingCart->hasBillingAddress() === false ) {
        if ( $OSCOM_Customer->hasDefaultAddress() ) {
          $OSCOM_ShoppingCart->setBillingAddress($OSCOM_Customer->getDefaultAddressID());
          $OSCOM_ShoppingCart->resetBillingMethod();
        } elseif ( $OSCOM_ShoppingCart->hasShippingAddress() ) {
          $OSCOM_ShoppingCart->setBillingAddress($OSCOM_ShoppingCart->getShippingAddress());
          $OSCOM_ShoppingCart->resetBillingMethod();
        }
      }

      if ( $OSCOM_ShoppingCart->hasBillingMethod() === false ) {
        $OSCOM_Payment = new Payment();

        if ( $OSCOM_Payment->numberOfActive() === 1 ) {
          $OSCOM_PaymentModule = Registry::get('Payment_' . reset($OSCOM_Payment->getActive()));

          $OSCOM_ShoppingCart->setBillingMethod(array('id' => $OSCOM_PaymentModule->getCode(),
                                                      'title' => $OSCOM_PaymentModule->getMethodTitle()));
        }
      }

      return $OSCOM_ShoppingCart->hasBillingAddress() && $OSCOM_ShoppingCart->hasBillingMethod();
    }

    public static function onFail(Product $OSCOM_Product) {
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');

      $OSOCM_NavigationHistory->setSnapshot();

      osc_redirect(OSCOM::getLink(null, 'Checkout', 'Billing', 'SSL'));
    }
  }
?>
