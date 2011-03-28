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
  use osCommerce\OM\Core\Site\Admin\Application\Configuration\Configuration;

  class EntryFind {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array('entries' => array());

      $Qcfg = $OSCOM_PDO->prepare('select * from :table_configuration where configuration_group_id = :configuration_group_id and (configuration_key like :configuration_key or configuration_value like :configuration_value) order by sort_order, configuration_title');
      $Qcfg->bindInt(':configuration_group_id', $data['group_id']);
      $Qcfg->bindValue(':configuration_key', '%' . $data['search'] . '%');
      $Qcfg->bindValue(':configuration_value', '%' . $data['search'] . '%');
      $Qcfg->execute();

      while ( $row = $Qcfg->fetch() ) {
        $result['entries'][] = $row;

        if ( !empty($row['use_function']) ) {
          $result['entries'][count($result['entries'])-1]['configuration_value'] = Configuration::callUserFunc($row['use_function'], $row['configuration_value']);
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
