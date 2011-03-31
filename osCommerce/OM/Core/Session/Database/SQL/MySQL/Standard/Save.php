<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Session\Database\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Save {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qsession = $OSCOM_PDO->prepare('replace into :table_sessions values (:id, :expiry, :value)');
      $Qsession->bindValue(':id', $data['id']);
      $Qsession->bindInt(':expiry', $data['expiry']);
      $Qsession->bindValue(':value', $data['value']);
      $Qsession->execute();

      return ( ($Qsession->rowCount() === 1) || !$Qsession->isError() );
    }
  }
?>
