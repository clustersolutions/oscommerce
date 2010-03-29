<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/applications/languages/classes/languages.php');

  class osC_Languages_Admin_rpc {
    public static function getAll() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !isset($_GET['page']) || !is_numeric($_GET['page']) ) {
        $_GET['page'] = 1;
      }

      if ( !empty($_GET['search']) ) {
        $result = osC_Languages_Admin::find($_GET['search'], $_GET['page']);
      } else {
        $result = osC_Languages_Admin::getAll($_GET['page']);
      }

      $result['rpcStatus'] = RPC_STATUS_SUCCESS;

      echo json_encode($result);
    }

    public static function getDefinitionGroups() {
      global $_module;

      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = osC_Languages_Admin::findDefinitionGroups($_GET[$_module], $_GET['search']);
      } else {
        $result = osC_Languages_Admin::getDefinitionGroups($_GET[$_module]);
      }

      $result['rpcStatus'] = RPC_STATUS_SUCCESS;

      echo json_encode($result);
    }

    public static function getDefinitions() {
      global $_module;

      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = osC_Languages_Admin::findDefinitions($_GET[$_module], $_GET['group'], $_GET['search']);
      } else {
        $result = osC_Languages_Admin::getDefinitions($_GET[$_module], $_GET['group']);
      }

      $result['rpcStatus'] = RPC_STATUS_SUCCESS;

      echo json_encode($result);
    }
  }
?>
