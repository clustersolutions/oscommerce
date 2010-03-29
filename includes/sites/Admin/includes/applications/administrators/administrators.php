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

  require('includes/applications/administrators/classes/administrators.php');

  class osC_Application_Administrators extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'administrators',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    public function __construct() {
      global $osC_Language;

      $this->_page_title = $osC_Language->get('heading_title');
    }
  }
?>
