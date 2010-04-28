<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Application_Configuration_RPC {
    public static function getAll() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = OSCOM_Site_Admin_Application_Configuration_Configuration::find($_GET['search']);
      } else {
        $result = OSCOM_Site_Admin_Application_Configuration_Configuration::getAll();
      }

      $result['rpcStatus'] = OSCOM_RPC::STATUS_SUCCESS;

      echo json_encode($result);
    }

    public static function getAllEntries() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = OSCOM_Site_Admin_Application_Configuration_Configuration::findEntries($_GET['search'], $_GET['id']);
      } else {
        $result = OSCOM_Site_Admin_Application_Configuration_Configuration::getAllEntries($_GET['id']);
      }

      $result['rpcStatus'] = OSCOM_RPC::STATUS_SUCCESS;

      echo json_encode($result);
    }
  }
?>
