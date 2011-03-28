<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\Cache;
  use osCommerce\OM\Core\Database;
  use osCommerce\OM\Core\MessageStack;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\PDO;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Template;

  class Controller implements \osCommerce\OM\Core\SiteInterface {
    protected static $_default_application = 'Index';

    public static function initialize() {
      require('includes/functions/compatibility.php');
      require('includes/functions/general.php');
      require('includes/functions/html_output.php');

      Registry::set('MessageStack', new MessageStack());
      Registry::set('Cache', new Cache());
      Registry::set('Database', Database::initialize());

      Registry::set('PDO', PDO::initialize());

      $Qcfg = Registry::get('Database')->query('select configuration_key as cfgKey, configuration_value as cfgValue from :table_configuration');
      $Qcfg->setCache('configuration');
      $Qcfg->execute();

      while ( $Qcfg->next() ) {
        define($Qcfg->value('cfgKey'), $Qcfg->value('cfgValue'));
      }

      $Qcfg->freeResult();

      Registry::set('Service', new Service());
      Registry::get('Service')->start();

      Registry::set('Template', new Template());

      $application = 'osCommerce\\OM\\Core\\Site\\Shop\\Application\\' . OSCOM::getSiteApplication() . '\\Controller';
      Registry::set('Application', new $application());

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
