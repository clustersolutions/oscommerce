<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Application_Countries_RPC {
    public static function getAll() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !isset($_GET['page']) || !is_numeric($_GET['page']) ) {
        $_GET['page'] = 1;
      }

      if ( !empty($_GET['search']) ) {
        $result = OSCOM_Site_Admin_Application_Countries_Countries::find($_GET['search'], $_GET['page']);
      } else {
        $result = OSCOM_Site_Admin_Application_Countries_Countries::getAll($_GET['page']);
      }

      $result['rpcStatus'] = RPC_STATUS_SUCCESS;

      echo json_encode($result);
    }

    public static function getAllZones() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = OSCOM_Site_Admin_Application_Countries_Countries::findZones($_GET['search'], $_GET['id']);
      } else {
        $result = OSCOM_Site_Admin_Application_Countries_Countries::getAllZones($_GET['id']);
      }

      $result['rpcStatus'] = RPC_STATUS_SUCCESS;

      echo json_encode($result);
    }
  }
?>
