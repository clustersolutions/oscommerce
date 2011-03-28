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

  class EntrySave {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qupdate = $OSCOM_PDO->prepare('update :table_configuration set configuration_value = :configuration_value, last_modified = now() where configuration_key = :configuration_key');
      $Qupdate->bindValue(':configuration_value', $data['value']);
      $Qupdate->bindValue(':configuration_key', $data['key']);
      $Qupdate->execute();

      return ( ($Qupdate->rowCount() === 1) || !$Qupdate->isError() );
    }
  }
?>
