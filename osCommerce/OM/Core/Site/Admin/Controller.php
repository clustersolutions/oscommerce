<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Cache;
  use osCommerce\OM\Core\Database;
  use osCommerce\OM\Core\Session;
  use osCommerce\OM\Core\Access;

  use osCommerce\OM\Core\DatabasePDO;

  define('OSC_IN_ADMIN', true);

  require(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/includes/functions/general.php');
  require(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/includes/functions/html_output.php');
  require(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/includes/functions/localization.php');

  class Controller implements \osCommerce\OM\Core\SiteInterface {
    protected static $_default_application = 'Index';
    protected static $_guest_applications = array('Index', 'Login');

    public static function initialize() {
      OSCOM::loadConfig();

      if ( strlen(DB_SERVER) < 1 ) {
        osc_redirect(OSCOM::getLink('Setup'));
      }

      Registry::set('MessageStack', new MessageStack());
      Registry::set('Cache', new Cache());
      Registry::set('Database', Database::initialize());

      Registry::set('PDO', DatabasePDO::initialize());

      $Qcfg = Registry::get('Database')->query('select configuration_key as cfgKey, configuration_value as cfgValue from :table_configuration');
      $Qcfg->setCache('configuration');
      $Qcfg->execute();

      while ( $Qcfg->next() ) {
        define($Qcfg->value('cfgKey'), $Qcfg->value('cfgValue'));
      }

      $Qcfg->freeResult();

      Registry::set('Session', Session::load('adminSid'));
      Registry::get('Session')->start();

      Registry::get('MessageStack')->loadFromSession();

      Registry::set('Language', new Language());

      if ( !self::hasAccess(OSCOM::getSiteApplication()) ) {
        Registry::get('MessageStack')->add('header', 'No access.', 'error');

        osc_redirect_admin(OSCOM::getLink(null, 'Index'));
      }

      $application = 'osCommerce\\OM\\Core\\Site\\Admin\\Application\\' . OSCOM::getSiteApplication() . '\\Controller';
      Registry::set('Application', new $application());

      Registry::set('Template', new Template());
      Registry::get('Template')->setApplication(Registry::get('Application'));

// HPDL move following checks elsewhere
// check if a default currency is set
      if (!defined('DEFAULT_CURRENCY')) {
        Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_error_no_default_currency'), 'error');
      }

// check if a default language is set
      if (!defined('DEFAULT_LANGUAGE')) {
        Registry::get('MessageStack')->add('header', ERROR_NO_DEFAULT_LANGUAGE_DEFINED, 'error');
      }

      if (function_exists('ini_get') && ((bool)ini_get('file_uploads') == false) ) {
        Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_warning_uploads_disabled'), 'warning');
      }
    }

    public static function getDefaultApplication() {
      return self::$_default_application;
    }

    public static function hasAccess($application) {
      if ( !isset($_SESSION[OSCOM::getSite()]['id']) ) {
        $redirect = false;

        if ( $application != 'Login' ) {
          $_SESSION[OSCOM::getSite()]['redirect_origin'] = $application;

          $redirect = true;
        }

        if ( $redirect === true ) {
          osc_redirect_admin(OSCOM::getLink(null, 'Login'));
        }
      }

      return Access::hasAccess(OSCOM::getSite(), $application);
    }

    public static function getGuestApplications() {
      return self::$_guest_applications;
    }
  }
?>
