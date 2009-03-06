<?php
/*
  $Id: login.php 176 2005-09-02 23:44:40 +0200 (Fr, 02 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Account_Logoff extends osC_Template {

/* Private variables */

    var $_module = 'logoff',
        $_group = 'account',
        $_page_title,
        $_page_contents = 'logoff.php';

/* Class constructor */

    function osC_Account_Logoff() {
      global $osC_Language, $osC_Services, $osC_Breadcrumb;

      $this->_page_title = $osC_Language->get('sign_out_heading');

      if ($osC_Services->isStarted('breadcrumb')) {
        $osC_Breadcrumb->add($osC_Language->get('breadcrumb_sign_out'));
      }

      $this->_process();
    }

/* Private methods */

    function _process() {
      global $osC_ShoppingCart, $osC_Customer;

      $osC_ShoppingCart->reset();

      $osC_Customer->reset();
    }
  }
?>
