<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Template extends OSCOM_Template {
    public function __construct() {
      $this->set('default');
    }

    public function getIcon($size = 16, $icon = null) {
      if ( empty($icon) ) {
        $icon = $this->_application->getIcon();
      }

      return '<img src="' . OSCOM::getPublicSiteLink('images/applications/' . (int)$size . '/' . $icon) . '" border="0" alt="" width="' . (int)$size . '" height="' . (int)$size . '" />';
    }
  }
?>
