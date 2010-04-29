<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Setup implements OSCOM_SiteInterface {
    protected static $_default_application = 'Index';

    public static function initialize() {
      define('OSCOM_BOOTSTRAP_FILE', 'index.php');
      define('HTTP_SERVER', '');
      define('HTTP_COOKIE_PATH', '');
      define('HTTP_COOKIE_DOMAIN', '');
      define('DIR_WS_HTTP_CATALOG', '');
      define('DIR_WS_IMAGES', '');
      define('DB_SERVER_PERSISTENT_CONNECTIONS', false);
      define('DIR_FS_WORK', OSCOM::BASE_DIRECTORY . 'work/');

      OSCOM_Registry::set('Language', new OSCOM_Site_Setup_Language());
      OSCOM_Registry::set('osC_Language', OSCOM_Registry::get('Language')); // HPDL to delete

      $application = 'OSCOM_Site_' . OSCOM::getSite() . '_Application_' . OSCOM::getSiteApplication();
      OSCOM_Registry::set('Application', new $application());

      OSCOM_Registry::set('Template', new OSCOM_Site_Setup_Template());
      OSCOM_Registry::get('Template')->setApplication(OSCOM_Registry::get('Application'));
    }

    public static function getDefaultApplication() {
      return self::$_default_application;
    }

    public static function hasAccess($application) {
      return true;
    }
  }
?>
