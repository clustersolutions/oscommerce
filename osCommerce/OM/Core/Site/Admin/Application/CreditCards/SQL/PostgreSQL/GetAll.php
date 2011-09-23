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

  class GetAll {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $sql_query = 'select * from :table_credit_cards order by sort_order, credit_card_name';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_max_results offset :batch_pageset';
      }

      $Qcc = $OSCOM_PDO->prepare($sql_query);

      if ( $data['batch_pageset'] !== -1 ) {
        $Qcc->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qcc->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qcc->execute();

      $result['entries'] = $Qcc->fetchAll();

      $Qtotal = $OSCOM_PDO->query('select count(*) from :table_credit_cards');
      $Qtotal->execute();

      $result['total'] = $Qtotal->fetchColumn();

      return $result;
    }
  }
?>
