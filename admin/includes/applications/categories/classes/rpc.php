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

  require('includes/applications/categories/classes/categories.php');
  require('includes/classes/category_tree.php');

  class osC_Categories_Admin_rpc {
    public static function getAll() {
      global $_module;

      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = osC_Categories_Admin::find($_GET['search'], $_GET[$_module]);
      } else {
        $result = osC_Categories_Admin::getAll($_GET[$_module]);
      }

      $result['rpcStatus'] = RPC_STATUS_SUCCESS;

      echo json_encode($result);
    }
  }
?>
