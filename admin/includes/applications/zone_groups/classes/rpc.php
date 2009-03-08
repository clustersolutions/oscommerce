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

  require('includes/applications/zone_groups/classes/zone_groups.php');

  class osC_Zone_groups_Admin_rpc {
    public static function getAll() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !isset($_GET['page']) || !is_numeric($_GET['page']) ) {
        $_GET['page'] = 1;
      }

      if ( !empty($_GET['search']) ) {
        $result = osC_ZoneGroups_Admin::find($_GET['search'], $_GET['page']);
      } else {
        $result = osC_ZoneGroups_Admin::getAll($_GET['page']);
      }

      $result['rpcStatus'] = RPC_STATUS_SUCCESS;

      echo json_encode($result);
    }

    public static function getAllEntries() {
      global $_module;

      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = osC_ZoneGroups_Admin::findEntries($_GET['search'], $_GET[$_module]);
      } else {
        $result = osC_ZoneGroups_Admin::getAllEntries($_GET[$_module]);
      }

      $result['rpcStatus'] = RPC_STATUS_SUCCESS;

      echo json_encode($result);
    }
  }
?>
