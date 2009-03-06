<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Application_Login_Actions_logoff extends osC_Application_Login {
    public function __construct() {
      global $osC_Language, $osC_MessageStack;

      parent::__construct();

      unset($_SESSION['admin']);

      $osC_MessageStack->add('header', $osC_Language->get('ms_success_logged_out'), 'success');

      osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT));
    }
  }
?>
