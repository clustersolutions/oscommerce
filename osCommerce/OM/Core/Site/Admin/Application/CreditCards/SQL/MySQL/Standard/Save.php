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

  class Save {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      if ( isset($data['id']) && is_numeric($data['id']) ) {
        $Qcc = $OSCOM_PDO->prepare('update :table_credit_cards set credit_card_name = :credit_card_name, pattern = :pattern, credit_card_status = :credit_card_status, sort_order = :sort_order where id = :id');
        $Qcc->bindInt(':id', $data['id']);
      } else {
        $Qcc = $OSCOM_PDO->prepare('insert into :table_credit_cards (credit_card_name, pattern, credit_card_status, sort_order) values (:credit_card_name, :pattern, :credit_card_status, :sort_order)');
      }

      $Qcc->bindValue(':credit_card_name', $data['name']);
      $Qcc->bindValue(':pattern', $data['pattern']);
      $Qcc->bindInt(':credit_card_status', $data['status']);
      $Qcc->bindInt(':sort_order', $data['sort_order']);
      $Qcc->execute();

      return ( ($Qcc->rowCount() === 1) || !$Qcc->isError() );
    }
  }
?>
