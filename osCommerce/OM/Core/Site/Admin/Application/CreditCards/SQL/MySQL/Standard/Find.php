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

  class Find {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $result = array();

      $sql_query = 'select SQL_CALC_FOUND_ROWS * from :table_credit_cards where (credit_card_name like :credit_card_name) order by credit_card_name';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_pageset, :batch_max_results';
      }

      $sql_query .= '; select found_rows();';

      $Qcc = $OSCOM_Database->prepare($sql_query);
      $Qcc->bindValue(':credit_card_name', '%' . $data['keywords'] . '%');

      if ( $data['batch_pageset'] !== -1 ) {
        $Qcc->bindInt(':batch_pageset', $OSCOM_Database->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qcc->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qcc->execute();

      $result['entries'] = $Qcc->fetchAll();

      $Qcc->nextRowset();

      $result['total'] = $Qcc->fetchColumn();

      return $result;
    }
  }
?>
