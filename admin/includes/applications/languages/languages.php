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

  require('includes/applications/languages/classes/languages.php');
  require('includes/applications/currencies/classes/currencies.php');

  class osC_Application_Languages extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'languages',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Language;

      $this->_page_title = $osC_Language->get('heading_title');

      if ( !empty($_GET[$this->_module]) && is_numeric($_GET[$this->_module]) && osC_Languages_Admin::exists($_GET[$this->_module]) ) {
        $this->_page_title .= ': ' . osC_Languages_Admin::get($_GET[$this->_module], 'name');
        $this->_page_contents = 'groups.php';

        if ( isset($_GET['group']) && !empty($_GET['group']) && osC_Languages_Admin::isDefinitionGroup($_GET[$this->_module], $_GET['group']) ) {
          $this->_page_title .= ': ' . $_GET['group'];
          $this->_page_contents = 'definitions.php';
        }
      }
    }
  }
?>
