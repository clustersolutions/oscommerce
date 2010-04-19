<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Setup_Application_Install extends OSCOM_Site_Setup_ApplicationAbstract {
    protected function initialize() {
      $this->_page_contents = 'step_1.php';
      $this->_page_title = OSCOM::getDef('heading_title');

      if ( isset($_GET['step']) && is_numeric($_GET['step']) ) {
        switch ( $_GET['step'] ) {
          case '2':
            $this->_page_contents = 'step_2.php';
            break;

          case '3':
            $this->_page_contents = 'step_3.php';
            break;
        }
      }
    }
  }
?>
