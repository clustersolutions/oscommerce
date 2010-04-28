<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Application_Configuration extends OSCOM_Site_Admin_ApplicationAbstract {
    protected $_group = 'configuration';
    protected $_icon = 'configuration.png';
    protected $_sort_order = 200;

    protected function initialize() {
      $this->_title = OSCOM::getDef('app_title');
    }

    protected function process() {
      $this->_page_title = OSCOM::getDef('heading_title');

      if ( isset($_GET['id']) && is_numeric($_GET['id']) ) {
        $this->_page_contents = 'entries.php';
        $this->_page_title .= ': ' . OSCOM_Site_Admin_Application_Configuration_Configuration::get($_GET['id'], 'configuration_group_title');
      }
    }
  }
?>
