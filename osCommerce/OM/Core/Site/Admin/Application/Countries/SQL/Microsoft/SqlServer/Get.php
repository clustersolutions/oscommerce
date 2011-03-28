<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\Microsoft\SqlServer;

  use osCommerce\OM\Core\Registry;

  class Get {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Q = $OSCOM_PDO->prepare('EXEC CountriesGet :countries_id');
      $Q->bindInt(':countries_id', $data['id']);
      $Q->execute();

      $result_1 = $Q->toArray();

      $Q->nextResultSet();

      $result_2 = $Q->toArray();

      return array_merge($result_1, $result_2);
    }
  }
?>
