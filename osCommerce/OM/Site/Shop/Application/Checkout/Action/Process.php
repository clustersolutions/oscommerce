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
  use osCommerce\OM\Site\Shop\Payment;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

// if no shipping method has been selected, redirect the customer to the shipping method selection page
      if ( ($OSCOM_ShoppingCart->hasShippingMethod() === false) && ($OSCOM_ShoppingCart->getContentType() != 'virtual') ) {
        osc_redirect(OSCOM::getLink(null, null, 'Shipping', 'SSL'));
      }

// load selected payment module
      Registry::set('Payment', new Payment($OSCOM_ShoppingCart->getBillingMethod('id')), true);
      $OSCOM_Payment = Registry::get('Payment');

      if ( $OSCOM_Payment->hasActive() && ($OSCOM_ShoppingCart->hasBillingMethod() === false) ) {
        osc_redirect(OSCOM::getLink(null, null, 'Billing', 'SSL'));
      }

      $OSCOM_Payment->process();

      osc_redirect(OSCOM::getLink(null, null, 'Success', 'SSL'));
    }
  }
?>
