<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\Access;
  use osCommerce\OM\Core\Cache;
  use osCommerce\OM\Core\PDO;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Session;

  class Controller implements \osCommerce\OM\Core\SiteInterface {
    protected static $_default_application = 'Dashboard';
    protected static $_guest_applications = array('Dashboard', 'Login');

    public static function initialize() {
      Registry::set('MessageStack', new MessageStack());
      Registry::set('Cache', new Cache());
      Registry::set('PDO', PDO::initialize());

      foreach ( OSCOM::callDB('Shop\GetConfiguration', null, 'Site') as $param ) {
        define($param['cfgKey'], $param['cfgValue']);
      }

      Registry::set('Session', Session::load('adminSid'));
      Registry::get('Session')->start();

      Registry::get('MessageStack')->loadFromSession();

      Registry::set('Language', new Language());

      if ( !self::hasAccess(OSCOM::getSiteApplication()) ) {
        Registry::get('MessageStack')->add('header', 'No access.', 'error');

        OSCOM::redirect(OSCOM::getLink(null, OSCOM::getDefaultSiteApplication()));
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
          OSCOM::redirect(OSCOM::getLink(null, 'Login'));
        }
      }

      return Access::hasAccess(OSCOM::getSite(), $application);
    }

    public static function getGuestApplications() {
      return self::$_guest_applications;
    }
  }
?>
