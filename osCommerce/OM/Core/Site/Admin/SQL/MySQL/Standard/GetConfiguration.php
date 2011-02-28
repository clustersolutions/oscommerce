<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetConfiguration {
    public static function execute() {
      $OSCOM_Database = Registry::get('PDO');

      $Qcfg = $OSCOM_Database->prepare('select configuration_key as cfgKey, configuration_value as cfgValue from :table_configuration');
//      $Qcfg->setCache('configuration'); HPDL
      $Qcfg->execute();

      return $Qcfg->fetchAll();
    }
  }
?>
