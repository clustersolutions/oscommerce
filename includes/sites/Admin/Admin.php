<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  define('OSC_IN_ADMIN', true);

  require(OSCOM::BASE_DIRECTORY . 'sites/Admin/includes/functions/general.php');
  require(OSCOM::BASE_DIRECTORY . 'sites/Admin/includes/functions/html_output.php');
  require(OSCOM::BASE_DIRECTORY . 'sites/Admin/classes/access.php');
  require(OSCOM::BASE_DIRECTORY . 'sites/Admin/includes/functions/localization.php');
  require(OSCOM::BASE_DIRECTORY . 'classes/object_info.php');

  class OSCOM_Admin implements OSCOM_SiteInterface {
    protected static $_default_application = 'Index';
    protected static $_guest_applications = array('Index', 'Login');

    public static function initialize() {
      OSCOM::loadConfig();

      if ( strlen(DB_SERVER) < 1 ) {
        osc_redirect(OSCOM::getLink('Setup'));
      }

      include(OSCOM::BASE_DIRECTORY . 'database_tables.php'); // HPDL to remove

      OSCOM_Registry::set('MessageStack', new OSCOM_Site_Admin_MessageStack());
      OSCOM_Registry::set('osC_MessageStack', OSCOM_Registry::get('MessageStack')); // HPDL to delete
      OSCOM_Registry::set('Cache', new OSCOM_Cache());
      OSCOM_Registry::set('osC_Cache', OSCOM_Registry::get('Cache')); // HPDL to delete
      OSCOM_Registry::set('Database', OSCOM_Database::initialize());
      OSCOM_Registry::set('osC_Database', OSCOM_Registry::get('Database')); // HPDL to delete

// set the application parameters
      $Qcfg = OSCOM_Registry::get('Database')->query('select configuration_key as cfgKey, configuration_value as cfgValue from :table_configuration');
      $Qcfg->setCache('configuration');
      $Qcfg->execute();

      while ( $Qcfg->next() ) {
        define($Qcfg->value('cfgKey'), $Qcfg->value('cfgValue'));
      }

      $Qcfg->freeResult();

      OSCOM_Registry::set('Session', OSCOM_Session::load('adminSid'));
      OSCOM_Registry::get('Session')->start();

      OSCOM_Registry::get('MessageStack')->loadFromSession();

      OSCOM_Registry::set('Language', new OSCOM_Site_Admin_Language());
      OSCOM_Registry::set('osC_Language', OSCOM_Registry::get('Language')); // HPDL to delete

      $_SESSION['module'] = OSCOM::getSiteApplication(); // HPDL to delete; use OSCOM::getSiteApplication()
      if ( !self::hasAccess(OSCOM::getSiteApplication()) ) {
        OSCOM_Registry::get('MessageStack')->add('header', 'No access.', 'error');

        osc_redirect_admin(OSCOM::getLink(null, 'Index'));
      }

      $application = 'OSCOM_Site_' . OSCOM::getSite() . '_Application_' . OSCOM::getSiteApplication();
      OSCOM_Registry::set('Application', new $application());

      OSCOM_Registry::set('Template', new OSCOM_Site_Admin_Template());
      OSCOM_Registry::set('osC_Template', OSCOM_Registry::get('Template')); // HPDL to remove
      OSCOM_Registry::get('Template')->setApplication(OSCOM_Registry::get('Application'));

// HPDL move following checks elsewhere
// check if a default currency is set
      if (!defined('DEFAULT_CURRENCY')) {
        OSCOM_Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_error_no_default_currency'), 'error');
      }

// check if a default language is set
      if (!defined('DEFAULT_LANGUAGE')) {
        OSCOM_Registry::get('MessageStack')->add('header', ERROR_NO_DEFAULT_LANGUAGE_DEFINED, 'error');
      }

      if (function_exists('ini_get') && ((bool)ini_get('file_uploads') == false) ) {
        OSCOM_Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_warning_uploads_disabled'), 'warning');
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

      return osC_Access::hasAccess(OSCOM::getSite(), $application);
    }

    public static function getGuestApplications() {
      return self::$_guest_applications;
    }
  }
?>
