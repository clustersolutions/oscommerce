<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Currencies\SQL\PostgreSQL;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class Find {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $sql_query = 'select * from :table_currencies where (title ilike :title or code ilike :code or symbol_left ilike :symbol_left or symbol_right ilike :symbol_right) order by title';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_max_results offset :batch_pageset';
      }

      $Qcurrencies = $OSCOM_PDO->prepare($sql_query);
      $Qcurrencies->bindValue(':title', '%' . $data['keywords'] . '%');
      $Qcurrencies->bindValue(':code', '%' . $data['keywords'] . '%');
      $Qcurrencies->bindValue(':symbol_left', '%' . $data['keywords'] . '%');
      $Qcurrencies->bindValue(':symbol_right', '%' . $data['keywords'] . '%');

      if ( $data['batch_pageset'] !== -1 ) {
        $Qcurrencies->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qcurrencies->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qcurrencies->execute();

      $result['entries'] = $Qcurrencies->fetchAll();

      $Qtotal = $OSCOM_PDO->prepare('select count(*) from :table_currencies where (title ilike :title or code ilike :code or symbol_left ilike :symbol_left or symbol_right ilike :symbol_right)');
      $Qtotal->bindValue(':title', '%' . $data['keywords'] . '%');
      $Qtotal->bindValue(':code', '%' . $data['keywords'] . '%');
      $Qtotal->bindValue(':symbol_left', '%' . $data['keywords'] . '%');
      $Qtotal->bindValue(':symbol_right', '%' . $data['keywords'] . '%');
      $Qtotal->execute();

      $result['total'] = $Qtotal->fetchColumn();

      return $result;
    }
  }
?>
