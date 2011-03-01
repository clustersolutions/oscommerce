<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Dashboard\Model;

  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\OSCOM;

  class getModules {
    public static function execute() {
      $OSCOM_DirectoryListing = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Module/Dashboard');
      $OSCOM_DirectoryListing->setIncludeDirectories(false);

      $result = array();

      foreach ( $OSCOM_DirectoryListing->getFiles() as $file ) {
        $module = substr($file['name'], 0, strrpos($file['name'], '.'));
        $module_class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Dashboard\\' . $module;

        $OSCOM_Admin_DB_Module = new $module_class();

        if ( $OSCOM_Admin_DB_Module->hasData() ) {
          $result[] = array('module' => $module,
                            'title' => $OSCOM_Admin_DB_Module->getTitle(),
                            'link' => $OSCOM_Admin_DB_Module->hasTitleLink() ? $OSCOM_Admin_DB_Module->getTitleLink() : null,
                            'data' => $OSCOM_Admin_DB_Module->getData());
        }
      }

      return $result;
    }
  }
?>
