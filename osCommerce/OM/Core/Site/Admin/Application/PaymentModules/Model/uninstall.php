<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\PaymentModules\Model;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Cache;

  class uninstall {
    public static function execute($module) {
      $OSCOM_Language = Registry::get('Language');

      $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Payment\\' . $module;

      if ( class_exists($class) ) {
        $OSCOM_Language->injectDefinitions('modules/payment/' . $module . '.xml');

        $OSCOM_PM = new $class();
        $OSCOM_PM->remove();

        Cache::clear('modules-payment');
        Cache::clear('configuration');

        return true;
      }

      return false;
    }
  }
?>
