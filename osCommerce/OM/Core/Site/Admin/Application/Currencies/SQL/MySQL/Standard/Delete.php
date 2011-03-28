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

  class Delete {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcheck = $OSCOM_PDO->prepare('select code from :table_currencies where currencies_id = :currencies_id');
      $Qcheck->bindInt(':currencies_id', $data['id']);
      $Qcheck->execute();

      if ( $Qcheck->value('code') != DEFAULT_CURRENCY ) {
        $Qdelete = $OSCOM_PDO->prepare('delete from :table_currencies where currencies_id = :currencies_id');
        $Qdelete->bindInt(':currencies_id', $data['id']);
        $Qdelete->execute();

        return ( $Qdelete->rowCount() === 1 );
      }

      return false;
    }
  }
?>
