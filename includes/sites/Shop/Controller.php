<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop;

  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Registry;
  use osCommerce\OM\MessageStack;
  use osCommerce\OM\Cache;
  use osCommerce\OM\Database;
  use osCommerce\OM\Session;
  use osCommerce\OM\Template;

  class Controller implements \osCommerce\OM\SiteInterface {
    protected static $_default_application = 'index';
    protected static $_application = 'index';

    public static function initialize() {
      OSCOM::loadConfig();

      if ( strlen(DB_SERVER) < 1 ) {
        osc_redirect(OSCOM::getLink('Setup'));
      }

      include(OSCOM::BASE_DIRECTORY . 'database_tables.php'); // HPDL to remove
      include(OSCOM::BASE_DIRECTORY . 'filenames.php'); // HPDL to remove

      Registry::set('MessageStack', new MessageStack());
      Registry::set('osC_MessageStack', Registry::get('MessageStack')); // HPDL to delete
      Registry::set('Cache', new Cache());
      Registry::set('osC_Cache', Registry::get('Cache')); // HPDL to delete
      Registry::set('Database', Database::initialize());
      Registry::set('osC_Database', Registry::get('Database')); // HPDL to delete

// set the application parameters
      $Qcfg = Registry::get('Database')->query('select configuration_key as cfgKey, configuration_value as cfgValue from :table_configuration');
      $Qcfg->setCache('configuration');
      $Qcfg->execute();

      while ( $Qcfg->next() ) {
        define($Qcfg->value('cfgKey'), $Qcfg->value('cfgValue'));
      }

      $Qcfg->freeResult();

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

      Registry::set('osC_Services', new osC_Services());
      Registry::get('osC_Services')->startServices();

      Registry::get('osC_Language')->load(self::$_application);

      Registry::set('Template', Template::setup(self::$_application));
      Registry::set('osC_Template', Registry::get('Template')); // HPDL to remove
    }

    public static function getDefaultApplication() {
      return self::$_default_application;
    }

    public static function hasAccess($application) {
      return true;
    }
  }
?>
