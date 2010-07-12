<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Checkout\Action\Billing;

  use osCommerce\OM\ApplicationAbstract;
  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Site\Shop\Payment;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      if ( isset($_POST['payment_method']) && !empty($_POST['payment_method']) ) {
        $OSCOM_Payment = new Payment($_POST['payment_method']);

        if ( $OSCOM_Payment->hasActive() && Registry::exists('Payment_' . $_POST['payment_method']) && (Registry::get('Payment_' . $_POST['payment_method']) instanceof Payment) && Registry::get('Payment_' . $_POST['payment_method'])->isEnabled() ) {
          $OSCOM_ShoppingCart->setBillingMethod(array('id' => Registry::get('Payment_' . $_POST['payment_method'])->getCode(),
                                                      'title' => Registry::get('Payment_' . $_POST['payment_method'])->getMethodTitle()));

          osc_redirect(OSCOM::getLink(null, null, null, 'SSL'));
        }
      }
    }
  }
?>
