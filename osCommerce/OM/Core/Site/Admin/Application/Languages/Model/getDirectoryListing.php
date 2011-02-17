<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\OSCOM;

  class getDirectoryListing {
    public static function execute() {
      $result = array();

      $OSCOM_DirectoryListing = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages');
      $OSCOM_DirectoryListing->setIncludeDirectories(false);
      $OSCOM_DirectoryListing->setCheckExtension('xml');

      foreach ( $OSCOM_DirectoryListing->getFiles() as $file ) {
        $result[] = substr($file['name'], 0, strrpos($file['name'], '.'));
      }

      return $result;
    }
  }
?>
