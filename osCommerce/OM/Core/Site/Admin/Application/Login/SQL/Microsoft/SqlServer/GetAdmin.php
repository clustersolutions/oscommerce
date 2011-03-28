<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Login\SQL\Microsoft\SqlServer;

  use osCommerce\OM\Core\Registry;

  class GetAdmin {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qadmin = $OSCOM_PDO->prepare('select top 1 id, user_name, user_password from :table_administrators where user_name = :user_name');
      $Qadmin->bindValue(':user_name', $data['username']);
      $Qadmin->execute();

      return $Qadmin->fetch();
    }
  }
?>
