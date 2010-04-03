<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  abstract class OSCOM_Site_Admin_ApplicationAbstract extends OSCOM_ApplicationAbstract {
    public function __construct() {
      $this->initialize();

      if ( isset($_GET['action']) && !empty($_GET['action']) ) {
        $action = osc_sanitize_string(basename($_GET['action']));

        if ( class_exists('OSCOM_Site_' . OSCOM::getSite() . '_Application_' . OSCOM::getSiteApplication() . '_Action_' . $action) ) {
          call_user_func(array('OSCOM_Site_' . OSCOM::getSite() . '_Application_' . OSCOM::getSiteApplication() . '_Action_' . $action, 'execute'), $this);
        }
      }
    }
  }
?>
