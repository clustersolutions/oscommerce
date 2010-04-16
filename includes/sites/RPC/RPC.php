<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  define('OSC_IN_ADMIN', true);
  define('RPC_STATUS_SUCCESS', 1);
  define('RPC_STATUS_NO_SESSION', -10);
  define('RPC_STATUS_NO_MODULE', -20);
  define('RPC_STATUS_NO_ACCESS', -50);
  define('RPC_STATUS_CLASS_NONEXISTENT', -60);
  define('RPC_STATUS_NO_ACTION', -70);
  define('RPC_STATUS_ACTION_NONEXISTENT', -71);

  require(OSCOM::BASE_DIRECTORY . 'sites/Admin/includes/functions/general.php');
  require(OSCOM::BASE_DIRECTORY . 'sites/Admin/includes/functions/html_output.php');
  require(OSCOM::BASE_DIRECTORY . 'sites/Admin/classes/access.php');
  require(OSCOM::BASE_DIRECTORY . 'sites/Admin/includes/functions/localization.php');
  require(OSCOM::BASE_DIRECTORY . 'classes/object_info.php');
  
  class OSCOM_RPC extends OSCOM_SiteAbstract {
    protected static $_guest_applications = array('Index', 'Login');

    public static function initialize() {
// set the application parameters
      $Qcfg = OSCOM_Registry::get('Database')->query('select configuration_key as cfgKey, configuration_value as cfgValue from :table_configuration');
      $Qcfg->setCache('configuration');
      $Qcfg->execute();

      while ( $Qcfg->next() ) {
        define($Qcfg->value('cfgKey'), $Qcfg->value('cfgValue'));
      }

      $Qcfg->freeResult();

      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

      OSCOM_Registry::set('Session', OSCOM_Session::load('adminSid'));
      OSCOM_Registry::get('Session')->start();

      OSCOM_Registry::set('osC_Language', new OSCOM_Site_RPC_Language());

      if ( !isset($_SESSION['admin']) ) {
        echo json_encode(array('rpcStatus' => RPC_STATUS_NO_SESSION));
        exit;
      }

      $module = null;
      $class = null;

      if ( empty($_GET) ) {
        echo json_encode(array('rpcStatus' => RPC_STATUS_NO_MODULE));
        exit;
      } else {
        $first_array = array_slice($_GET, 1, 1);
        $_module = osc_sanitize_string(basename(key($first_array)));

        if ( !osC_Access::hasAccess($_module) ) {
          echo json_encode(array('rpcStatus' => RPC_STATUS_NO_ACCESS));
          exit;
        }

        $class = (isset($_GET['class']) && !empty($_GET['class'])) ? osc_sanitize_string(basename($_GET['class'])) : 'RPC';
        $action = (isset($_GET['action']) && !empty($_GET['action'])) ? osc_sanitize_string(basename($_GET['action'])) : '';

        if ( empty($action) ) {
          echo json_encode(array('rpcStatus' => RPC_STATUS_NO_ACTION));
          exit;
        }

        if ( class_exists('OSCOM_Site_Admin_Application_' . $_module . '_' . $class) ) {
          if ( method_exists('OSCOM_Site_Admin_Application_' . $_module . '_' . $class, $action) ) {
            call_user_func(array('OSCOM_Site_Admin_Application_' . $_module . '_' . $class, $action));
            exit;
          } else {
            echo json_encode(array('rpcStatus' => RPC_STATUS_ACTION_NONEXISTENT));
            exit;
          }
        } else {
          echo json_encode(array('rpcStatus' => RPC_STATUS_CLASS_NONEXISTENT));
          exit;
        }
      }
    }

    public static function getGuestApplications() {
      return self::$_guest_applications;
    }
  }
?>
