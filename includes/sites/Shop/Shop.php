<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Shop extends OSCOM_SiteAbstract {
    protected static $_default_application = 'index';
    protected static $_application = 'index';

    public static function initialize() {
      if ( !empty($_GET) ) {
        $requested_application = osc_sanitize_string(basename(key(array_slice($_GET, 0, 1))));

        if ( $requested_application == OSCOM::getSite() ) {
          $requested_application = osc_sanitize_string(basename(key(array_slice($_GET, 1, 1))));
        }

        if ( !empty($requested_application) ) {
          if ( file_exists(OSCOM::BASE_DIRECTORY . 'sites/Shop/applications/' . $requested_application . '/' . $requested_application . '.php') ) {
            self::$_application = $requested_application;
          }
        }
      }

      OSCOM_Registry::set('osC_Services', new osC_Services());
      OSCOM_Registry::get('osC_Services')->startServices();

      OSCOM_Registry::get('osC_Language')->load(self::$_application);

      OSCOM_Registry::set('osC_Template', OSCOM_Template::setup(self::$_application));
    }
  }
?>
