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

  class GetGroups {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $result = array();

      $Qgroups = $OSCOM_Database->prepare('select distinct content_group, count(*) as total_entries from :table_languages_definitions where languages_id = :languages_id group by content_group order by content_group');
      $Qgroups->bindInt(':languages_id', $data['id']);
      $Qgroups->execute();

      $result['entries'] = $Qgroups->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
