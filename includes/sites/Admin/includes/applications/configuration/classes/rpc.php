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

  require('includes/applications/configuration/classes/configuration.php');

  class osC_Configuration_Admin_rpc {
    public static function getAll() {
      if ( !isset($_GET['gID']) ) {
        $_GET['gID'] = '1';
      }

      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = osC_Configuration_Admin::find($_GET['search']);
      } else {
        $result = osC_Configuration_Admin::getAll($_GET['gID']);
      }

      $result['rpcStatus'] = RPC_STATUS_SUCCESS;

      echo json_encode($result);
    }
  }
?>
