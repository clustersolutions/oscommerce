<?php
/*
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  $osCommerce-SIG$

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Application_Login extends osC_Template_Admin {
    protected $_module = 'login';
    protected $_page_title;
    protected $_page_contents = 'main.php';

    public function __construct() {
      $this->_page_title = __('heading_title');
    }
  }
?>
