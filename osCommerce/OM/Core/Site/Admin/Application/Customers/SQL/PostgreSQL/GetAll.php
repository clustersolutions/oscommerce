<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Customers\SQL\PostgreSQL;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class GetAll {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $sql_query = 'select c.customers_id, c.customers_gender, c.customers_firstname, c.customers_lastname, c.customers_status, c.date_account_created, a.entry_country_id from :table_customers c left join :table_address_book a on (c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id) order by c.customers_lastname, c.customers_firstname';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_max_results offset :batch_pageset';
      }

      $Qcustomers = $OSCOM_PDO->prepare($sql_query);

      if ( $data['batch_pageset'] !== -1 ) {
        $Qcustomers->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qcustomers->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qcustomers->execute();

      $result['entries'] = $Qcustomers->fetchAll();

      $Qtotal = $OSCOM_PDO->query('select count(*) from :table_customers');
      $Qtotal->execute();

      $result['total'] = $Qtotal->fetchColumn();

      return $result;
    }
  }
?>
