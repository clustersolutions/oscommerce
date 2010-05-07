<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop;

  use osCommerce\OM\OSCOM;

  abstract class ApplicationAbstract extends \osCommerce\OM\ApplicationAbstract {
    public function __construct($process = true) {
      $this->initialize();

      if ( $process === true ) {
        $this->process();

        if ( isset($_GET['action']) && !empty($_GET['action']) ) {
          $action = osc_sanitize_string(basename($_GET['action']));

          if ( class_exists('osCommerce\\OM\\Site\\Shop\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . $action) ) {
            call_user_func(array('osCommerce\\OM\\Site\\Shop\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . $action, 'execute'), $this);
          }
        }
      }
    }
  }
?>
