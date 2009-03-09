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

  require('includes/applications/tax_classes/classes/tax_classes.php');
  require('includes/applications/zone_groups/classes/zone_groups.php');

  class osC_Application_Tax_classes extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'tax_classes',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Language;

      $this->_page_title = $osC_Language->get('heading_title');

      if ( !empty($_GET[$this->_module]) && is_numeric($_GET[$this->_module]) ) {
        $this->_page_contents = 'entries.php';
        $this->_page_title .= ': ' . osC_TaxClasses_Admin::get($_GET[$this->_module], 'tax_class_title');
      }
    }
  }
?>
