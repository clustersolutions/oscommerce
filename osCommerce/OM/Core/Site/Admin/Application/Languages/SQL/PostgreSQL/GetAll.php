<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\SQL\PostgreSQL;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class GetAll {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $sql_query = 'select l.*, (select count(*) from :table_languages_definitions ld where ld.languages_id = l.languages_id) as total_definitions from :table_languages l order by l.sort_order, l.name';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_max_results offset :batch_pageset';
      }

      $Qlanguages = $OSCOM_PDO->prepare($sql_query);

      if ( $data['batch_pageset'] !== -1 ) {
        $Qlanguages->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qlanguages->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qlanguages->execute();

      $result['entries'] = $Qlanguages->fetchAll();

      $Qtotal = $OSCOM_PDO->query('select count(*) from :table_languages');
      $Qtotal->execute();

      $result['total'] = $Qtotal->fetchColumn();

      return $result;
    }
  }
?>
