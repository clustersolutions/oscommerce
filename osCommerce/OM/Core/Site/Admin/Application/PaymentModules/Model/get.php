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

  class get {
    public static function execute($code) {
      $OSCOM_Language = Registry::get('Language');

      $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Payment\\' . $code;

      $OSCOM_Language->injectDefinitions('modules/payment/' . $code . '.xml');

      $OSCOM_PM = new $class();

      $result = array('code' => $OSCOM_PM->getCode(),
                      'title' => $OSCOM_PM->getTitle(),
                      'sort_order' => $OSCOM_PM->getSortOrder(),
                      'status' => $OSCOM_PM->isEnabled(),
                      'keys' => $OSCOM_PM->getKeys());

      return $result;
    }
  }
?>
