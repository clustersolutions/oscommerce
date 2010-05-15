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
  use osCommerce\OM\Template;

  class Controller implements \osCommerce\OM\SiteInterface {
    protected static $_default_application = 'Index';

    public static function initialize() {
      OSCOM::loadConfig();

      if ( strlen(DB_SERVER) < 1 ) {
        osc_redirect(OSCOM::getLink('Setup'));
      }

      Registry::set('MessageStack', new MessageStack());
      Registry::set('Cache', new Cache());
      Registry::set('Database', Database::initialize());

      $Qcfg = Registry::get('Database')->query('select configuration_key as cfgKey, configuration_value as cfgValue from :table_configuration');
      $Qcfg->setCache('configuration');
      $Qcfg->execute();

      while ( $Qcfg->next() ) {
        define($Qcfg->value('cfgKey'), $Qcfg->value('cfgValue'));
      }

      $Qcfg->freeResult();

      Registry::set('Service', new Service());
      Registry::get('Service')->start();

      $application = 'osCommerce\\OM\\Site\\Shop\\Application\\' . OSCOM::getSiteApplication() . '\\Controller';
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
