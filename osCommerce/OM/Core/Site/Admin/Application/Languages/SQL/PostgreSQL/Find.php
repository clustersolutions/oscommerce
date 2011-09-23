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

  class Find {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $sql_query = 'select distinct l.*, (select count(*) from :table_languages_definitions ld where ld.languages_id = l.languages_id) as total_definitions from :table_languages l join :table_languages_definitions ld on (ld.languages_id = l.languages_id) where (l.name ilike :name or l.code ilike :code or ld.definition_key ilike :definition_key or ld.definition_value ilike :definition_value) order by l.name';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_max_results offset :batch_pageset';
      }

      $Qlanguages = $OSCOM_PDO->prepare($sql_query);
      $Qlanguages->bindValue(':name', '%' . $data['keywords'] . '%');
      $Qlanguages->bindValue(':code', '%' . $data['keywords'] . '%');
      $Qlanguages->bindValue(':definition_key', '%' . $data['keywords'] . '%');
      $Qlanguages->bindValue(':definition_value', '%' . $data['keywords'] . '%');

      if ( $data['batch_pageset'] !== -1 ) {
        $Qlanguages->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qlanguages->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qlanguages->execute();

      $result['entries'] = $Qlanguages->fetchAll();

      $Qtotal = $OSCOM_PDO->prepare('select count(distinct l.languages_id) from :table_languages l join :table_languages_definitions ld on (ld.languages_id = l.languages_id) where (l.name ilike :name or l.code ilike :code or ld.definition_key ilike :definition_key or ld.definition_value ilike :definition_value)');
      $Qtotal->bindValue(':name', '%' . $data['keywords'] . '%');
      $Qtotal->bindValue(':code', '%' . $data['keywords'] . '%');
      $Qtotal->bindValue(':definition_key', '%' . $data['keywords'] . '%');
      $Qtotal->bindValue(':definition_value', '%' . $data['keywords'] . '%');
      $Qtotal->execute();

      $result['total'] = $Qtotal->fetchColumn();

      return $result;
    }
  }
?>
