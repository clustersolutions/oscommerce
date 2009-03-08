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

  require('includes/applications/error_log/classes/error_log.php');

  class osC_Error_log_Admin_rpc {
    public static function getAll() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !isset($_GET['page']) || !is_numeric($_GET['page']) ) {
        $_GET['page'] = 1;
      }

      if ( !empty($_GET['search']) ) {
        $result = osC_ErrorLog_Admin::find($_GET['search'], $_GET['page']);
      } else {
        $result = osC_ErrorLog_Admin::getAll($_GET['page']);
      }

      $result['rpcStatus'] = RPC_STATUS_SUCCESS;

      echo json_encode($result);
    }
  }
?>
