<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Currencies\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class UpdateRate {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $Qupdate = $OSCOM_Database->prepare('update :table_currencies set value = :value, last_updated = now() where currencies_id = :currencies_id');
      $Qupdate->bindValue(':value', $data['rate']);
      $Qupdate->bindInt(':currencies_id', $data['id']);
      $Qupdate->execute();

      return ( ($Qupdate->rowCount() === 1) || !$Qupdate->isError() );
    }
  }
?>
