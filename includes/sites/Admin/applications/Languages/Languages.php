<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Application_Languages extends OSCOM_Site_Admin_ApplicationAbstract {
    protected function initialize() {
      $this->_page_title = OSCOM::getDef('heading_title');

      if ( !empty($_GET['id']) && is_numeric($_GET['id']) && OSCOM_Site_Admin_Application_Languages_Languages::exists($_GET['id']) ) {
        $this->_page_title .= ': ' . OSCOM_Site_Admin_Application_Languages_Languages::get($_GET['id'], 'name');
        $this->_page_contents = 'groups.php';

        if ( isset($_GET['group']) && !empty($_GET['group']) && OSCOM_Site_Admin_Application_Languages_Languages::isDefinitionGroup($_GET['id'], $_GET['group']) ) {
          $this->_page_title .= ': ' . $_GET['group'];
          $this->_page_contents = 'definitions.php';
        }
      }
    }
  }
?>
