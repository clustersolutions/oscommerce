<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Checkout_Success extends osC_Template {
    protected $_module = 'success';
    protected $_group = 'checkout';
    protected $_page_title;
    protected $_page_contents = 'success.php';

    public function __construct() {
      global $osC_Services, $osC_Breadcrumb;

      $this->_page_title = __('success_heading');

      if ( $osC_Services->isStarted('breadcrumb') ) {
        $osC_Breadcrumb->add(__('breadcrumb_checkout_success'), osc_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

      if ( $_GET[$this->_module] == 'update' ) {
        $this->_process();
      }
    }

    protected function _process() {
      global $osC_Customer;

      $notify_string = '';

      if ( $osC_Customer->isLoggedOn() ) {
        $products_array = (isset($_POST['notify']) ? $_POST['notify'] : array());

        if ( !is_array($products_array) ) {
          $products_array = array($products_array);
        }

        $notifications = array();

        foreach ( $products_array as $product_id ) {
          if ( is_numeric($product_id) && !in_array($product_id, $notifications) ) {
            $notifications[] = $product_id;
          }
        }

        if ( !empty($notifications) ) {
          $notify_string = 'action=notify_add&products=' . implode(';', $notifications);
        }
      }

      osc_redirect(osc_href_link(FILENAME_DEFAULT, $notify_string, 'AUTO'));
    }
  }
?>
