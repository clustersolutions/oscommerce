<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetAccessUserLevels {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qaccess = $OSCOM_PDO->prepare('select module from :table_administrators_access where administrators_id = :administrators_id');
      $Qaccess->bindInt(':administrators_id', $data['id']);
      $Qaccess->execute();

      return $Qaccess->fetchAll();
    }
  }
?>
