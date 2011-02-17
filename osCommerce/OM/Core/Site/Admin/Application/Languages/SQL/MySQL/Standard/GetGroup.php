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

  class GetGroup {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $result = array();

      $Qgroup = $OSCOM_Database->prepare('select languages_id, count(*) as total_entries from :table_languages_definitions where content_group = :content_group group by languages_id');
      $Qgroup->bindValue(':content_group', $data['group']);
      $Qgroup->execute();

      $result['entries'] = $Qgroup->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
