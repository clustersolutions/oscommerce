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

  class Get {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qgroup = $OSCOM_PDO->prepare('select cg.*, count(c.configuration_id) as total_entries from :table_configuration_group cg left join :table_configuration c on (cg.configuration_group_id = c.configuration_group_id) where cg.configuration_group_id = :configuration_group_id');
      $Qgroup->bindInt(':configuration_group_id', $data['id']);
      $Qgroup->execute();

      return $Qgroup->fetch();
    }
  }
?>
