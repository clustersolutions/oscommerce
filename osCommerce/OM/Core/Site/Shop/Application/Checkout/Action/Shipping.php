<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Application\Checkout\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\Site\Shop\Product;
  use osCommerce\OM\Core\Site\Shop\Shipping as ShippingClass;

  class Shipping {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
      $OSCOM_Template = Registry::get('Template');
      $OSCOM_Customer = Registry::get('Customer');

      global $osC_oiAddress; // HPDL

      $application->setPageTitle(OSCOM::getDef('shipping_method_heading'));
      $application->setPageContent('shipping.php');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_checkout_shipping'), OSCOM::getLink(null, null, 'Shipping', 'SSL'));
      }

// load shipping address page if no default address exists
      if ( !$OSCOM_ShoppingCart->hasShippingAddress() ) {
        $application->setPageTitle(OSCOM::getDef('shipping_address_heading'));
        $application->setPageContent('shipping_address.php');

        $OSCOM_Template->addJavascriptFilename('templates/' . $OSCOM_Template->getCode() . '/javascript/checkout_shipping_address.js');
        $OSCOM_Template->addJavascriptPhpFilename('includes/form_check.js.php');

        if ( !$OSCOM_Customer->isLoggedOn() ) {
          $osC_oiAddress = new ObjectInfo($OSCOM_ShoppingCart->getShippingAddress());
        }
      } else {
        $OSCOM_Template->addJavascriptFilename('templates/' . $OSCOM_Template->getCode() . '/javascript/checkout_shipping.js');

// load all enabled shipping modules
        Registry::set('Shipping', new ShippingClass(), true);
      }
    }
  }
?>
