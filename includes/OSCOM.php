<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM {
    const TIMESTAMP_START = OSCOM_TIMESTAMP_START;
    const BASE_DIRECTORY = OSCOM_BASE_DIRECTORY;

    protected static $_version;
    protected static $_request_type;
    protected static $_site;
    protected static $_application;

    public static function initialize() {
      spl_autoload_register('self::autoload');

      require(self::BASE_DIRECTORY . 'functions/compatibility.php');
      require(self::BASE_DIRECTORY . 'functions/general.php');
      require(self::BASE_DIRECTORY . 'functions/html_output.php');

      self::setSite();

      if ( !file_exists(self::BASE_DIRECTORY . 'sites/' . self::getSite() . '/' . self::getSite() . '.php') ) {
        trigger_error('Site \'' . self::getSite() . '\' does not exist', E_USER_ERROR);
        exit();
      }

      include(self::BASE_DIRECTORY . 'sites/' . self::getSite() . '/' . self::getSite() . '.php');

      self::setSiteApplication();

      call_user_func(array('OSCOM_' . self::getSite(), 'initialize'));
    }

    public static function siteExists($site) {
      return file_exists(self::BASE_DIRECTORY . 'sites/' . $site . '/' . $site . '.php');
    }

    public static function setSite($site = null) {
      if ( isset($site) ) {
        if ( !self::siteExists($site) ) {
          trigger_error('Site \'' . $site . '\' does not exist, using default \'' . self::getDefaultSite() . '\'', E_USER_ERROR);
          $site = self::getDefaultSite();
        }
      } else {
        $site = self::getDefaultSite();

        if ( !empty($_GET) ) {
          $requested_site = osc_sanitize_string(basename(key(array_slice($_GET, 0, 1))));

          if ( self::siteExists($requested_site) ) {
            $site = $requested_site;
          }
        }
      }

      if ( !empty($site) ) {
        self::$_site = $site;
      }
    }

    public static function getSite() {
      return self::$_site;
    }

    public static function getDefaultSite() {
      if ( defined('OSCOM_DEFAULT_SITE') ) {
        return OSCOM_DEFAULT_SITE;
      }

      return 'Shop';
    }

    public static function siteApplicationExists($application) {
      return file_exists(OSCOM::BASE_DIRECTORY . 'sites/' . self::getSite() . '/applications/' . $application . '/' . $application . '.php');
    }

    public static function setSiteApplication($application = null) {
      if ( isset($application) ) {
        if ( !self::siteApplicationExists($application) ) {
          trigger_error('Application \'' . $application . '\' does not exist for Site \'' . self::getSite() . '\', using default \'' . self::getDefaultSiteApplication() . '\'', E_USER_ERROR);
          $application = self::getDefaultSiteApplication();
        }
      } else {
        if ( !empty($_GET) ) {
          $requested_application = osc_sanitize_string(basename(key(array_slice($_GET, 0, 1))));

          if ( $requested_application == self::getSite() ) {
            $requested_application = osc_sanitize_string(basename(key(array_slice($_GET, 1, 1))));
          }

          if ( empty($requested_application) ) {
            $application = self::getDefaultSiteApplication();
          } else {
            if ( self::siteApplicationExists($requested_application) ) {
              $application = $requested_application;
            }
          }
        }
      }

      if ( !empty($application) ) {
        self::$_application = $application;
      }
    }

    public static function getSiteApplication() {
      return self::$_application;
    }

    public static function getDefaultSiteApplication() {
      return call_user_func(array('OSCOM_' . self::getSite(), 'getDefaultApplication'));
    }

    public static function autoload($class) {
      $namespace = explode('_', $class);

      $class_file = '';

      if ( $namespace[1] == 'Site' ) {
        if ( $namespace[3] == 'Application' ) {
          if ( isset($namespace[5]) ) {
            if ( $namespace[5] == 'Action' ) {
              $class_file = self::BASE_DIRECTORY . 'sites/' . $namespace[2] . '/applications/' . $namespace[4] . '/actions/' . implode('/', array_slice($namespace, 6)) . '.php';
            } else {
              $class_file = self::BASE_DIRECTORY . 'sites/' . $namespace[2] . '/applications/' . $namespace[4] . '/classes/' . implode('/', array_slice($namespace, 5)) . '.php';
            }
          } else {
            $class_file = self::BASE_DIRECTORY . 'sites/' . $namespace[2] . '/applications/' . $namespace[4] . '/' . $namespace[4] . '.php';
          }
        } elseif ( $namespace[3] == 'Module' ) {
          $class_file = self::BASE_DIRECTORY . 'sites/' . $namespace[2] . '/modules/' . implode('/', array_slice($namespace, 4)) . '.php';
        } else {
          $class_file = self::BASE_DIRECTORY . 'sites/' . $namespace[2] . '/classes/' . implode('/', array_slice($namespace, 3)) . '.php';
        }
      } else {
        $class_file = self::BASE_DIRECTORY . 'classes/' . implode('/', array_slice($namespace, 1)) . '.php';
      }

      if ( !empty($class_file) ) {
        if ( file_exists($class_file) ) {
          include($class_file);
        }
      }
    }

    public static function loadConfig() {
      $ini = parse_ini_file(self::BASE_DIRECTORY . 'config.php');

      if ( file_exists(self::BASE_DIRECTORY . 'local/config.php') ) {
        $local = parse_ini_file(self::BASE_DIRECTORY . 'local/config.php');

        $ini = array_merge($ini, $local);
      }

      foreach ( $ini as $key => $value ) {
        if ( strtolower($value) == 'true' ) {
          $value = true;
        } elseif ( strtolower($value) == 'false' ) {
          $value = false;
        }

        define($key, $value);
      }

      if ( self::getRequestType() == 'NONSSL' ) { // HPDL to remove
        define('DIR_WS_CATALOG', DIR_WS_HTTP_CATALOG);
      } else {
        define('DIR_WS_CATALOG', DIR_WS_HTTPS_CATALOG);
      }
    }

    protected static function setVersion() {
      self::$_version = '3.0';
    }

    public static function getVersion() {
      if ( !isset(self::$_version) ) {
        self::setVersion();
      }

      return self::$_version;
    }

    protected static function setRequestType() {
      self::$_request_type = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on') ? 'SSL' : 'NONSSL');
    }

    public static function getRequestType() {
      if ( !isset(self::$_request_type) ) {
        self::setRequestType();
      }

      return self::$_request_type;
    }

/**
 * Return an internal URL address.
 *
 * @param string $site The Site to link to. Default: The currently used Site.
 * @param string $application The Site Application to link to. Default: The currently used Site Application.
 * @param string $parameters Parameters to add to the link. Example: key1=value1&key2=value2
 * @param string $connection The type of connection to use for the link. Values: NONSSL, SSL, AUTO. Default: NONSSL.
 * @param bool $add_session_id Add the session ID to the link. Default: True.
 * @param bool $search_engine_safe Use search engine safe URLs. Default: True.
 * @return string The URL address.
 */

    public static function getLink($site = null, $application = null, $parameters = null, $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
      if ( empty($site) ) {
        $site = self::getSite();
      }

      if ( empty($application) ) {
        $application = self::getSiteApplication();
      }

      if ( !in_array($connection, array('NONSSL', 'SSL', 'AUTO')) ) {
        $connection = 'NONSSL';
      }

      if ( !is_bool($add_session_id) ) {
        $add_session_id = true;
      }

      if ( !is_bool($search_engine_safe) ) {
        $search_engine_safe = true;
      }

      if ( $connection == 'AUTO' ) {
        if ( (self::getRequestType() == 'SSL') && (ENABLE_SSL === true) ) {
          $link = HTTPS_SERVER . DIR_WS_HTTPS_CATALOG;
        } else {
          $link = HTTP_SERVER . DIR_WS_HTTP_CATALOG;
        }
      } elseif ( ($connection == 'SSL') && (ENABLE_SSL === true) ) {
        $link = HTTPS_SERVER . DIR_WS_HTTPS_CATALOG;
      } else {
        $link = HTTP_SERVER . DIR_WS_HTTP_CATALOG;
      }

      $link .= 'index.php?';

      if ( $site != self::getDefaultSite() ) {
        $link .= $site . '&';
      }

      if ( !empty($application) ) {
        $link .= $application . '&';
      }

      if ( !empty($parameters) ) {
        $link .= $parameters . '&';
      }

      if ( ($add_session_id === true) && OSCOM_Registry::exists('Session') && OSCOM_Registry::get('Session')->hasStarted() && (SERVICE_SESSION_FORCE_COOKIE_USAGE == '-1') ) {
        if ( strlen(SID) > 0 ) {
          $_sid = SID;
        } elseif ( ((self::getRequestType() == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL === true)) || ((self::getRequestType() == 'SSL') && ($connection != 'SSL')) ) {
          if ( HTTP_COOKIE_DOMAIN != HTTPS_COOKIE_DOMAIN ) {
            $_sid = OSCOM_Registry::get('Session')->getName() . '=' . OSCOM_Registry::get('Session')->getID();
          }
        }
      }

      if ( isset($_sid) ) {
        $link .= osc_output_string($_sid);
      }

      while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) {
        $link = substr($link, 0, -1);
      }

      if ( ($search_engine_safe === true) && OSCOM_Registry::exists('osC_Services') && OSCOM_Registry::get('osC_Services')->isStarted('sefu') ) {
        $link = str_replace(array('?', '&', '='), array('/', '/', ','), $link);
      }

      return $link;
    }

/**
 * Return an internal URL address for public objects.
 *
 * @param string $url The object location from the public/sites/SITE/ directory.
 * @param string $parameters Parameters to add to the link. Example: key1=value1&key2=value2
 * @return string The URL address.
 */

    public static function getPublicSiteLink($url, $parameters = null) {
      $link = 'public/sites/' . self::getSite() . '/' . $url;

      if ( !empty($parameters) ) {
        $link .= '?' . $parameters;
      }

      while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) {
        $link = substr($link, 0, -1);
      }

      return $link;
    }

/**
 * Return an internal URL address for an RPC call.
 *
 * @param string $site The Site to link to. Default: The currently used Site.
 * @param string $application The Site Application to link to. Default: The currently used Site Application.
 * @param string $parameters Parameters to add to the link. Example: key1=value1&key2=value2
 * @param string $connection The type of connection to use for the link. Values: NONSSL, SSL, AUTO. Default: NONSSL.
 * @param bool $add_session_id Add the session ID to the link. Default: True.
 * @param bool $search_engine_safe Use search engine safe URLs. Default: True.
 * @return string The URL address.
 */

    public static function getRPCLink($site = null, $application = null, $parameters = null, $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
      if ( empty($site) ) {
        $site = self::getSite();
      }

      if ( empty($application) ) {
        $application = self::getSiteApplication();
      }

      return self::getLink('RPC', $site, $application . '&' . $parameters, $connection, $add_session_id, $search_engine_safe);
    }

/**
 * Return a language definition
 *
 * @param string $key The language definition to return
 * @return string The language definition
 * @access public
 */

    public static function getDef($key) {
      return OSCOM_Registry::get('Language')->get($key);
    }
  }
?>
