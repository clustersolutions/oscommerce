<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Access_TaxClasses extends osC_Access {
    var $_module = 'TaxClasses',
        $_group = 'configuration',
        $_icon = 'classes.png',
        $_title,
        $_sort_order = 800;

    public function __construct() {
      $this->_title = OSCOM::getDef('access_tax_classes_title');
    }
  }
?>
