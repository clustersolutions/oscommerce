<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\CreditCards\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Get {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcc = $OSCOM_PDO->prepare('select * from :table_credit_cards where id = :id');
      $Qcc->bindInt(':id', $data['id']);
      $Qcc->execute();

      return $Qcc->fetch();
    }
  }
?>
