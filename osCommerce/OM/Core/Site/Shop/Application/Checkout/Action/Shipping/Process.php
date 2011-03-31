<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Application\Checkout\Action\Shipping;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Shipping = Registry::get('Shipping');
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      if ( !empty($_POST['comments']) ) {
        $_SESSION['comments'] = HTML::sanitize($_POST['comments']);
      }

      if ( $OSCOM_Shipping->hasQuotes() ) {
        if ( isset($_POST['shipping_mod_sel']) && strpos($_POST['shipping_mod_sel'], '_') ) {
          list($module, $method) = explode('_', $_POST['shipping_mod_sel']);

          if ( Registry::exists('Shipping_' . $module) && Registry::get('Shipping_' . $module)->isEnabled() ) {
            $quote = $OSCOM_Shipping->getQuote($_POST['shipping_mod_sel']);

            if ( isset($quote['error']) ) {
              $OSCOM_ShoppingCart->resetShippingMethod();
            } else {
              $OSCOM_ShoppingCart->setShippingMethod($quote);

              OSCOM::redirect(OSCOM::getLink(null, null, null, 'SSL'));
            }
          } else {
            $OSCOM_ShoppingCart->resetShippingMethod();
          }
        }
      } else {
        $OSCOM_ShoppingCart->resetShippingMethod();

        OSCOM::redirect(OSCOM::getLink(null, null, null, 'SSL'));
      }
    }
  }
?>
