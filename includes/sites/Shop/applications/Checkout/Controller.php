<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Checkout;

  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Site\Shop\Product;

  class Controller extends \osCommerce\OM\Site\Shop\ApplicationAbstract {
    protected function initialize() {}

    protected function process() {
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      $OSCOM_Language->load('checkout');

      if ( isset($_GET['action']) && ($_GET['action'] == 'email') ) {
        $this->_processEmailAddress();
      }

      $this->_page_title = OSCOM::getDef('shopping_cart_heading');
      $this->_page_contents = 'shopping_cart.php';

      if ( !$OSCOM_ShoppingCart->hasContents() ) {
        $this->_page_contents = 'shopping_cart_empty.php';
      }

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_checkout'), OSCOM::getLink(null, null, null, 'SSL'));
      }
    }

    public function requireCustomerAccount() {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      foreach ( $OSCOM_ShoppingCart->getProducts() as $product ) {
        $OSCOM_Product = new Product($product['id']);

        if ( $OSCOM_Product->isTypeActionAllowed(array('PerformOrder', 'RequireCustomerAccount'), null, false) ) {
          return true;
        }
      }

      return false;
    }

    protected function _processEmailAddress() {
      global $osC_Customer, $osC_MessageStack;

      if ( isset($_POST['email']) && (strlen(trim($_POST['email'])) >= ACCOUNT_EMAIL_ADDRESS) ) {
        if ( osc_validate_email_address($_POST['email']) ) {
          $osC_Customer->setEmailAddress(trim($_POST['email']));

          osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'confirmation', 'SSL'));
        } else {
          $osC_MessageStack->add($this->_module, __('field_customer_email_address_check_error'));
        }
      } else {
        $osC_MessageStack->add($this->_module, sprintf(__('field_customer_email_address_error'), ACCOUNT_EMAIL_ADDRESS));
      }
    }
  }
?>
