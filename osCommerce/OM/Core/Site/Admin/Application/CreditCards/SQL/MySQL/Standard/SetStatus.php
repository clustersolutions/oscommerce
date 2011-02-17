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

  class SetStatus {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $Qcc = $OSCOM_Database->prepare('update :table_credit_cards set credit_card_status = :credit_card_status where id = :id');
      $Qcc->bindInt(':credit_card_status', ($data['status'] === true) ? 1 : 0);
      $Qcc->bindInt(':id', $data['id']);
      $Qcc->execute();

      return ( ($Qcc->rowCount() === 1) || !$Qcc->isError() );
    }
  }
?>
