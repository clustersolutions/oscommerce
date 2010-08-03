<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\PaymentModules;

  use osCommerce\OM\Core\Site\RPC\Controller as OSCOM_Site_RPC;

  class RPC {
    public static function getInstalled() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = PaymentModules::findInstalled($_GET['search']);
      } else {
        $result = PaymentModules::getInstalled();
      }

      $result['rpcStatus'] = OSCOM_Site_RPC::STATUS_SUCCESS;

      echo json_encode($result);
    }

    public static function getUninstalled() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = PaymentModules::findUninstalled($_GET['search']);
      } else {
        $result = PaymentModules::getUninstalled();
      }

      $result['rpcStatus'] = OSCOM_Site_RPC::STATUS_SUCCESS;

      echo json_encode($result);
    }
  }
?>
