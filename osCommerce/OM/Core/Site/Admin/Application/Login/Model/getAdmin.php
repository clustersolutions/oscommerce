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

  class getAdmin {
    public static function execute($username, $key = null) {
      $data = array('username' => $username);

      $result = OSCOM::callDB('Admin\Login\GetAdmin', $data);

      if ( !empty($key) && isset($result[$key]) ) {
        $result = $result[$key];
      }

      return $result;
    }
  }
?>
