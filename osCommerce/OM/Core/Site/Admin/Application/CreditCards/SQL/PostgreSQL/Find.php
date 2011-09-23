<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\CreditCards\SQL\PostgreSQL;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class Find {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $sql_query = 'select * from :table_credit_cards where (credit_card_name ilike :credit_card_name) order by credit_card_name';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_max_results offset :batch_pageset';
      }

      $Qcc = $OSCOM_PDO->prepare($sql_query);
      $Qcc->bindValue(':credit_card_name', '%' . $data['keywords'] . '%');

      if ( $data['batch_pageset'] !== -1 ) {
        $Qcc->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qcc->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qcc->execute();

      $result['entries'] = $Qcc->fetchAll();

      $Qtotal = $OSCOM_PDO->prepare('select count(*) from :table_credit_cards where (credit_card_name ilike :credit_card_name)');
      $Qtotal->bindValue(':credit_card_name', '%' . $data['keywords'] . '%');
      $Qtotal->execute();

      $result['total'] = $Qtotal->fetchColumn();

      return $result;
    }
  }
?>
