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

        $action = null;

        if ( count($_GET) > 1 ) {
          $requested_action = osc_sanitize_string(basename(key(array_slice($_GET, 1, 1))));

          if ( $requested_action == OSCOM::getSiteApplication() ) {
            $requested_action = null;

            if ( count($_GET) > 2 ) {
              $requested_action = osc_sanitize_string(basename(key(array_slice($_GET, 2, 1))));
            }
          }

          if ( !empty($requested_action) && self::siteApplicationActionExists($requested_action) ) {
            $action = $requested_action;
          }
        }

        if ( !empty($action) ) {
          call_user_func(array('osCommerce\\OM\\Site\\Shop\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . $action, 'execute'), $this);
        }
      }
    }

    public function siteApplicationActionExists($action) {
      return class_exists('osCommerce\\OM\\Site\\Shop\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . $action);
    }
  }
?>
