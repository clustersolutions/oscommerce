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
  use osCommerce\OM\Core\Site\Admin\Application\PaymentModules\PaymentModules;
  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\OSCOM;

  class getUninstalled {
    public static function execute() {
      $OSCOM_Language = Registry::get('Language');

      $installed_modules = PaymentModules::getInstalled();
      $installed = array();

      foreach ( $installed_modules['entries'] as $module ) {
        $installed[] = $module['code'];
      }

      $result = array('entries' => array());

      $DLpm = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Module/Payment');
      $DLpm->setIncludeDirectories(false);

      foreach ( $DLpm->getFiles() as $file ) {
        $module = substr($file['name'], 0, strrpos($file['name'], '.'));

        if ( !in_array($module, $installed) ) {
          $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Payment\\' . $module;

          $OSCOM_Language->injectDefinitions('modules/payment/' . $module . '.xml');

          $OSCOM_PM = new $class();

          $result['entries'][] = array('code' => $OSCOM_PM->getCode(),
                                       'title' => $OSCOM_PM->getTitle(),
                                       'sort_order' => $OSCOM_PM->getSortOrder(),
                                       'status' => $OSCOM_PM->isEnabled());
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
