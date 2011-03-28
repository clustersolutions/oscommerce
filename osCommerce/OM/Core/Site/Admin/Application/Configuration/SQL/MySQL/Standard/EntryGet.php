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

  class EntryGet {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcfg = $OSCOM_PDO->prepare('select * from :table_configuration where configuration_id = :configuration_id');
      $Qcfg->bindInt(':configuration_id', $data['id']);
      $Qcfg->execute();

      return $Qcfg->fetch();
    }
  }
?>
