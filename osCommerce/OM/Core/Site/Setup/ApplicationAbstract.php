<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Setup;

  use osCommerce\OM\OSCOM;

  abstract class ApplicationAbstract extends \osCommerce\OM\ApplicationAbstract {
    public function __construct() {
      $this->initialize();

      if ( isset($_GET['action']) && !empty($_GET['action']) ) {
        $action = osc_sanitize_string(basename($_GET['action']));

        if ( class_exists('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . $action) ) {
          call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . $action, 'execute'), $this);
        }
      }
    }
  }
?>
