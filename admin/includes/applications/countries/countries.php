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

  require('includes/applications/countries/classes/countries.php');

  class osC_Application_Countries extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'countries',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    public function __construct() {
      global $osC_Language;

      $this->_page_title = $osC_Language->get('heading_title');

      if ( !empty($_GET[$this->_module]) && is_numeric($_GET[$this->_module]) ) {
        $this->_page_contents = 'zones.php';
        $this->_page_title .= ': ' . osC_Address::getCountryName($_GET[$this->_module]);
      }
    }
  }
?>
