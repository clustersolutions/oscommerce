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

  class Get {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $sql_query = 'select * from :table_currencies where';

      if ( is_numeric($data['id']) ) {
        $sql_query .= ' currencies_id = :currencies_id';
      } else {
        $sql_query .= ' code = :code';
      }

      $sql_query .= ' limit 1';

      $Qcurrency = $OSCOM_PDO->prepare($sql_query);

      if ( is_numeric($data['id']) ) {
        $Qcurrency->bindInt(':currencies_id', $data['id']);
      } else {
        $Qcurrency->bindValue(':code', $data['id']);
      }

      $Qcurrency->execute();

      return $Qcurrency->fetch();
    }
  }
?>
