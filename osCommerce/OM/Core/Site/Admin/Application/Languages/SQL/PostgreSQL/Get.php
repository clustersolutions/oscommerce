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

  class Get {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $sql_query = 'select l.*, (select count(*) from :table_languages_definitions ld where ld.languages_id = l.languages_id) as total_definitions from :table_languages l where';

      if ( is_numeric($data['id']) ) {
        $sql_query .= ' l.languages_id = :languages_id';
      } else {
        $sql_query .= ' l.code = :code';
      }

      $sql_query .= ' limit 1';

      $Qlanguage = $OSCOM_PDO->prepare($sql_query);

      if ( is_numeric($data['id']) ) {
        $Qlanguage->bindInt(':languages_id', $data['id']);
      } else {
        $Qlanguage->bindValue(':code', $data['id']);
      }

      $Qlanguage->execute();

      return $Qlanguage->fetch();
    }
  }
?>
