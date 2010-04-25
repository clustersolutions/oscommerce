<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Application_Index extends OSCOM_Site_Admin_ApplicationAbstract {
    protected $_link_to = false;
    protected $_icon = 'oscommerce.png';

    protected function initialize() {}

    protected function process() {
      $this->_page_title = OSCOM::getDef('heading_title');
    }
  }
?>
