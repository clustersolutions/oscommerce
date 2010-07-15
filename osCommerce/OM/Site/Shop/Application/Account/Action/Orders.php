<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Account\Action;

  use osCommerce\OM\ApplicationAbstract;
  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Site\Shop\Order;

  class Orders {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      if ( $OSCOM_Customer->isLoggedOn() === false ) {
        $OSCOM_NavigationHistory->setSnapshot();

        osc_redirect(OSCOM::getLink(null, null, 'LogIn', 'SSL'));
      }

      $application->setPageTitle(OSCOM::getDef('orders_heading'));
      $application->setPageContent('orders.php');

      $OSCOM_Language->load('order');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_my_orders'), OSCOM::getLink(null, null, 'Orders', 'SSL'));

        if ( is_numeric($_GET['Orders']) ) {
          $OSCOM_Breadcrumb->add(sprintf(OSCOM::getDef('breadcrumb_order_information'), $_GET['Orders']), OSCOM::getLink(null, null, 'Orders=' . $_GET['Orders'], 'SSL'));
        }
      }

      if ( is_numeric($_GET['Orders']) ) {
        if ( Order::getCustomerID($_GET['Orders']) !== $OSCOM_Customer->getID() ) {
          osc_redirect(OSCOM::getLink(null, null, null, 'SSL'));
        }

        $application->setPageTitle(sprintf(OSCOM::getDef('order_information_heading'), $_GET['Orders']));
        $application->setPageContent('orders_info.php');
      }
    }
  }
?>
