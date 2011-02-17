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

  class GetDefinitions {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $result = array();

      $sql_query = 'select * from :table_languages_definitions where languages_id = :languages_id and';

      if ( is_array($data['group']) ) {
        $sql_query .= ' content_group in ("' . implode('", "', $data['group']) . '")';
      } else {
        $sql_query .= ' content_group = :content_group';
      }

      $sql_query .= ' order by content_group, definition_key';

      $Qdefs = $OSCOM_Database->prepare($sql_query);

      if ( !is_array($data['group']) ) {
        $Qdefs->bindValue(':content_group', $data['group']);
      }

      $Qdefs->bindInt(':languages_id', $data['id']);
      $Qdefs->execute();

      $result['entries'] = $Qdefs->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
