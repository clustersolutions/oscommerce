<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Application_Languages_RPC {
    public static function getAll() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !isset($_GET['page']) || !is_numeric($_GET['page']) ) {
        $_GET['page'] = 1;
      }

      if ( !empty($_GET['search']) ) {
        $result = OSCOM_Site_Admin_Application_Languages_Languages::find($_GET['search'], $_GET['page']);
      } else {
        $result = OSCOM_Site_Admin_Application_Languages_Languages::getAll($_GET['page']);
      }

      $result['rpcStatus'] = OSCOM_RPC::STATUS_SUCCESS;

      echo json_encode($result);
    }

    public static function getDefinitionGroups() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = OSCOM_Site_Admin_Application_Languages_Languages::findDefinitionGroups($_GET['id'], $_GET['search']);
      } else {
        $result = OSCOM_Site_Admin_Application_Languages_Languages::getDefinitionGroups($_GET['id']);
      }

      $result['rpcStatus'] = OSCOM_RPC::STATUS_SUCCESS;

      echo json_encode($result);
    }

    public static function getDefinitions() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = OSCOM_Site_Admin_Application_Languages_Languages::findDefinitions($_GET['id'], $_GET['group'], $_GET['search']);
      } else {
        $result = OSCOM_Site_Admin_Application_Languages_Languages::getDefinitions($_GET['id'], $_GET['group']);
      }

      $result['rpcStatus'] = OSCOM_RPC::STATUS_SUCCESS;

      echo json_encode($result);
    }
  }
?>
