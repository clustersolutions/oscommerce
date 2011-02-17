<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Login\Model;

  use osCommerce\OM\Core\OSCOM;

  class isValidCredentials {
    public static function execute($data) {
      $result = OSCOM::callDB('Admin\Login\GetAdmin', array('username' => $data['username']));

      if ( !empty($result) ) {
        return osc_validate_password($data['password'], $result['user_password']);
      }

      return false;
    }
  }
?>
