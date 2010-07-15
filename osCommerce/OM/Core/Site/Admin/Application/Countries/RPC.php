<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries;

  use osCommerce\OM\Core\Site\RPC\Controller as OSCOM_Site_RPC;

  class RPC {
    public static function getAll() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !isset($_GET['page']) || !is_numeric($_GET['page']) ) {
        $_GET['page'] = 1;
      }

      if ( !empty($_GET['search']) ) {
        $result = Countries::find($_GET['search'], $_GET['page']);
      } else {
        $result = Countries::getAll($_GET['page']);
      }

      $result['rpcStatus'] = OSCOM_Site_RPC::STATUS_SUCCESS;

      echo json_encode($result);
    }

    public static function getAllZones() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = Countries::findZones($_GET['search'], $_GET['id']);
      } else {
        $result = Countries::getAllZones($_GET['id']);
      }

      $result['rpcStatus'] = OSCOM_Site_RPC::STATUS_SUCCESS;

      echo json_encode($result);
    }
  }
?>
