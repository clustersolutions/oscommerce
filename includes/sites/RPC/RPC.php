<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site;

  use osCommerce\OM\SiteInterface;
  use osCommerce\OM\OSCOM;

  class RPC implements SiteInterface {
    const STATUS_SUCCESS = 1;
    const STATUS_NO_SESSION = -10;
    const STATUS_NO_MODULE = -20;
    const STATUS_NO_ACCESS = -50;
    const STATUS_CLASS_NONEXISTENT = -60;
    const STATUS_NO_ACTION = -70;
    const STATUS_ACTION_NONEXISTENT = -71;

    protected static $_default_application = 'Index';

    public static function initialize() {
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

      if ( empty($_GET) ) {
        echo json_encode(array('rpcStatus' => self::STATUS_NO_MODULE));
        exit;
      } else {
        $site = osc_sanitize_string(basename(key(array_slice($_GET, 1, 1))));
        $application = osc_sanitize_string(basename(key(array_slice($_GET, 2, 1))));

        if ( !OSCOM::siteExists($site) ) {
          echo json_encode(array('rpcStatus' => self::STATUS_CLASS_NONEXISTENT));
          exit;
        }

        OSCOM::setSite($site);

        if ( !OSCOM::siteApplicationExists($application) ) {
          echo json_encode(array('rpcStatus' => self::STATUS_CLASS_NONEXISTENT));
          exit;
        }

        OSCOM::setSiteApplication($application);

        include(OSCOM::BASE_DIRECTORY . 'sites/' . $site . '/' . $site . '.php');
        call_user_func(array('osCommerce\\OM\\Site\\' . $site, 'initialize'));

        if ( !call_user_func(array('osCommerce\\OM\\Site\\' . $site, 'hasAccess'), $application)) {
          echo json_encode(array('rpcStatus' => self::STATUS_NO_ACCESS));
          exit;
        }

        $class = (isset($_GET['class']) && !empty($_GET['class'])) ? osc_sanitize_string(basename($_GET['class'])) : 'RPC';
        $action = (isset($_GET['action']) && !empty($_GET['action'])) ? osc_sanitize_string(basename($_GET['action'])) : '';

        if ( empty($action) ) {
          echo json_encode(array('rpcStatus' => self::STATUS_NO_ACTION));
          exit;
        }

        if ( class_exists('osCommerce\\OM\\Site\\' . $site . '\\Application\\' . $application . '\\' . $class) ) {
          if ( method_exists('osCommerce\\OM\\Site\\' . $site . '\\Application\\' . $application . '\\' . $class, $action) ) {
            call_user_func(array('osCommerce\\OM\\Site\\' . $site . '\\Application\\' . $application . '\\' . $class, $action));
            exit;
          } else {
            echo json_encode(array('rpcStatus' => self::STATUS_ACTION_NONEXISTENT));
            exit;
          }
        } else {
          echo json_encode(array('rpcStatus' => self::STATUS_CLASS_NONEXISTENT));
          exit;
        }
      }
    }

    public static function getDefaultApplication() {
      return self::$_default_application;
    }

    public static function hasAccess($application) {
      return true;
    }
  }
?>
