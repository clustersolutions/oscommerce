<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Dashboard\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetShortcuts {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qshortcuts = $OSCOM_PDO->prepare('select module, last_viewed from :table_administrator_shortcuts where administrators_id = :administrators_id');
      $Qshortcuts->bindInt(':administrators_id', $data['admin_id']);
      $Qshortcuts->execute();

      return $Qshortcuts->fetchAll();
    }
  }
?>
