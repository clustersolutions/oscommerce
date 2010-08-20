<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\CreditCards\RPC;

  use osCommerce\OM\Core\Site\Admin\Application\CreditCards\CreditCards;
  use osCommerce\OM\Core\Site\RPC\Controller as RPC;

  class GetAll {
    public static function execute() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !isset($_GET['page']) || !is_numeric($_GET['page']) ) {
        $_GET['page'] = 1;
      }

      if ( !empty($_GET['search']) ) {
        $result = CreditCards::find($_GET['search'], $_GET['page']);
      } else {
        $result = CreditCards::getAll($_GET['page']);
      }

      $result['rpcStatus'] = RPC::STATUS_SUCCESS;

      echo json_encode($result);
    }
  }
?>
