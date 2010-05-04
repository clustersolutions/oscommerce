<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site;

  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Registry;
  use osCommerce\OM\Site\Setup\Language;
  use osCommerce\OM\Site\Setup\Template;

  class Setup implements \osCommerce\OM\SiteInterface {
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

      Registry::set('Language', new Language());
      Registry::set('osC_Language', Registry::get('Language')); // HPDL to remove

      $application = 'osCommerce\\OM\\Site\\Setup\\Application\\' . OSCOM::getSiteApplication() . '\\Controller';
      Registry::set('Application', new $application());

      Registry::set('Template', new Template());
      Registry::get('Template')->setApplication(Registry::get('Application'));
    }

    public static function getDefaultApplication() {
      return self::$_default_application;
    }

    public static function hasAccess($application) {
      return true;
    }
  }
?>
