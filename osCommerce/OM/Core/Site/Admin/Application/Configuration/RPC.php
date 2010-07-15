<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration;

  use osCommerce\OM\Core\Site\RPC\Controller as OSCOM_Site_RPC;

  class RPC {
    public static function getAll() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = Configuration::find($_GET['search']);
      } else {
        $result = Configuration::getAll();
      }

      $result['rpcStatus'] = OSCOM_Site_RPC::STATUS_SUCCESS;

      echo json_encode($result);
    }

    public static function getAllEntries() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = Configuration::findEntries($_GET['search'], $_GET['id']);
      } else {
        $result = Configuration::getAllEntries($_GET['id']);
      }

      $result['rpcStatus'] = OSCOM_Site_RPC::STATUS_SUCCESS;

      echo json_encode($result);
    }
  }
?>
