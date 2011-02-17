<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Get {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $sql_query = 'select l.*, count(ld.languages_id) as total_definitions from :table_languages l left join :table_languages_definitions ld on (l.languages_id = ld.languages_id) where';

      if ( is_numeric($data['id']) ) {
        $sql_query .= ' l.languages_id = :languages_id';
      } else {
        $sql_query .= ' l.code = :code';
      }

      $sql_query .= ' limit 1';

      $Qlanguage = $OSCOM_Database->prepare($sql_query);

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
