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

  class FindGroups {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $result = array();

      $Qgroups = $OSCOM_Database->prepare('select distinct content_group, count(*) as total_entries from :table_languages_definitions where languages_id = :languages_id and (definition_key like :definition_key or definition_value like :definition_value) group by content_group order by content_group');
      $Qgroups->bindInt(':languages_id', $data['id']);
      $Qgroups->bindValue(':definition_key', '%' . $data['keywords'] . '%');
      $Qgroups->bindValue(':definition_value', '%' . $data['keywords'] . '%');
      $Qgroups->execute();

      $result['entries'] = $Qgroups->fetchAll();

      $result['total'] = count($result['entries']);;

      return $result;
    }
  }
?>
