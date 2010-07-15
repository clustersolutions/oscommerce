<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Checkout\Action;

  use osCommerce\OM\ApplicationAbstract;

  class Callback {
    public static function execute(ApplicationAbstract $application) {
      if ( isset($_GET['module']) && !empty($_GET['module']) ) {
        $module = osc_sanitize_string($_GET['module']);

        if ( class_exists('osCommerce\\OM\\Site\\Shop\\Module\\Payment\\' . $module) ) {
          $module = 'osCommerce\\OM\\Site\\Shop\\Module\\Payment\\' . $module;
          $module = new $module();
          $module->callback();
        }
      }

      exit;
    }
  }
?>
