<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop;

  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;

  abstract class ApplicationAbstract extends \osCommerce\OM\ApplicationAbstract {
    public function __construct($process = true) {
      $OSCOM_Session = Registry::get('Session');

      $this->initialize();

      if ( $process === true ) {
        $this->process();

        $action = null;
        $action_index = 1;

        if ( count($_GET) > 1 ) {
          $requested_action = osc_sanitize_string(basename(key(array_slice($_GET, 1, 1, true))));

          if ( $requested_action == OSCOM::getSiteApplication() ) {
            $requested_action = null;

            if ( count($_GET) > 2 ) {
              $requested_action = osc_sanitize_string(basename(key(array_slice($_GET, 2, 1, true))));

              $action_index = 2;
            }
          }

          if ( !empty($requested_action) && self::siteApplicationActionExists($requested_action) ) {
            $action = $requested_action;
          }
        }

        if ( isset($action) ) {
          call_user_func(array('osCommerce\\OM\\Site\\Shop\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . $action, 'execute'), $this);

          $action_index++;

          if ( $action_index < count($_GET) ) {
            $action = array($action);

            for ( $i = $action_index, $n = count($_GET); $i < $n; $i++ ) {
              $subaction = osc_sanitize_string(basename(key(array_slice($_GET, $i, 1, true))));

              if ( $subaction != $OSCOM_Session->getName() && self::siteApplicationActionExists(implode('\\', $action) . '\\' . $subaction) ) {
                call_user_func(array('osCommerce\\OM\\Site\\Shop\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . implode('\\', $action) . '\\' . $subaction, 'execute'), $this);

                $action[] = $subaction;
              } else {
                break;
              }
            }
          }
        }
      }
    }

    public function siteApplicationActionExists($action) {
      return class_exists('osCommerce\\OM\\Site\\Shop\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . $action);
    }
  }
?>
