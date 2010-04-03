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

  class OSCOM_Admin extends OSCOM_SiteAbstract {
    protected static $_default_application = 'Index';
    protected static $_guest_applications = array('Index', 'Login');

    public static function initialize() {
      OSCOM_Registry::set('MessageStack', new OSCOM_Site_Admin_MessageStack(), true); // initialize before session due to register_shutdown_function

      OSCOM_Registry::set('Session', OSCOM_Session::load('adminSid'));
      OSCOM_Registry::get('Session')->start();

      OSCOM_Registry::get('MessageStack')->loadFromSession();

      if ( !isset($_SESSION['admin']) ) {
        $redirect = false;

        if ( OSCOM::getSiteApplication() != 'Login' ) {
          if ( !isset($_SESSION['redirect_origin']) ) {
            $_SESSION['redirect_origin'] = OSCOM::getSiteApplication();
          }

          $redirect = true;
        }

        if ( $redirect === true ) {
          osc_redirect_admin(OSCOM::getLink(OSCOM::getSite(), 'Login'));
        }

        unset($redirect);
      }

      OSCOM_Registry::set('osC_Language', new OSCOM_Site_Admin_Language());

      $_SESSION['module'] = OSCOM::getSiteApplication(); // HPDL to delete; use OSCOM::getSiteApplication()
      if ( !osC_Access::hasAccess(OSCOM::getSiteApplication()) ) {
        OSCOM_Registry::get('MessageStack')->add('header', 'No access.', 'error');

        osc_redirect_admin(OSCOM::getLink());
      }

      $application = 'OSCOM_Site_' . OSCOM::getSite() . '_Application_' . OSCOM::getSiteApplication();
      OSCOM_Registry::set('Application', new $application());

      OSCOM_Registry::set('osC_Template', new OSCOM_Site_Admin_Template());
      OSCOM_Registry::get('osC_Template')->setApplication(OSCOM_Registry::get('Application'));

// HPDL move following checks elsewhere
// check if a default currency is set
      if (!defined('DEFAULT_CURRENCY')) {
        OSCOM_Registry::get('MessageStack')->add('header', __('ms_error_no_default_currency'), 'error');
      }

// check if a default language is set
      if (!defined('DEFAULT_LANGUAGE')) {
        OSCOM_Registry::get('MessageStack')->add('header', ERROR_NO_DEFAULT_LANGUAGE_DEFINED, 'error');
      }

      if (function_exists('ini_get') && ((bool)ini_get('file_uploads') == false) ) {
        OSCOM_Registry::get('MessageStack')->add('header', __('ms_warning_uploads_disabled'), 'warning');
      }
    }
 
    public static function getGuestApplications() {
      return self::$_guest_applications;
    }
  }
?>
