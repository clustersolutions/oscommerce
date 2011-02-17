<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Find {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $result = array();

      $Qgroups = $OSCOM_Database->prepare('select distinct cg.configuration_group_id, cg.configuration_group_title, count(c.configuration_id) as total_entries from :table_configuration_group cg, :table_configuration c where (c.configuration_key like :configuration_key or c.configuration_value like :configuration_value) and c.configuration_group_id = cg.configuration_group_id and cg.visible = 1 group by cg.configuration_group_id order by cg.sort_order, cg.configuration_group_title');
      $Qgroups->bindValue(':configuration_key', '%' . $data['search'] . '%');
      $Qgroups->bindValue(':configuration_value', '%' . $data['search'] . '%');
      $Qgroups->execute();

      $result['entries'] = $Qgroups->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
