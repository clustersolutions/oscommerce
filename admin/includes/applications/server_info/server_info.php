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

  class osC_Application_Server_info extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'server_info',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Language;

      $this->_page_title = $osC_Language->get('heading_title');

      $this->_image = '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';

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'image':
            header('Content-Type: image/gif');

            echo base64_decode($this->_image);

            exit;

            break;

          case 'phpInfo':
            phpinfo();

            exit;

            break;
        }
      }
    }
  }
?>
