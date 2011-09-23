<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Administrators\SQL\PostgreSQL;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class Find {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $sql_query = 'select * from :table_administrators where (user_name ilike :user_name) order by user_name';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_max_results offset :batch_pageset';
      }

      $Qadmins = $OSCOM_PDO->prepare($sql_query);
      $Qadmins->bindValue(':user_name', '%' . $data['user_name'] . '%');

      if ( $data['batch_pageset'] !== -1 ) {
        $Qadmins->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qadmins->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qadmins->execute();

      $result['entries'] = $Qadmins->fetchAll();

      $Qtotal = $OSCOM_PDO->prepare('select count(*) from :table_administrators where (user_name ilike :user_name)');
      $Qtotal->bindValue(':user_name', '%' . $data['user_name'] . '%');
      $Qtotal->execute();

      $result['total'] = $Qtotal->fetchColumn();

      return $result;
    }
  }
?>
