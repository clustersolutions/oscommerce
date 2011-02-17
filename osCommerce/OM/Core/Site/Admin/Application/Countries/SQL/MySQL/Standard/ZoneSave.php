<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class ZoneSave {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      if ( isset($data['id']) && is_numeric($data['id']) ) {
        $Qzone = $OSCOM_Database->prepare('update :table_zones set zone_name = :zone_name, zone_code = :zone_code, zone_country_id = :zone_country_id where zone_id = :zone_id');
        $Qzone->bindInt(':zone_id', $data['id']);
      } else {
        $Qzone = $OSCOM_Database->prepare('insert into :table_zones (zone_name, zone_code, zone_country_id) values (:zone_name, :zone_code, :zone_country_id)');
      }

      $Qzone->bindValue(':zone_name', $data['name']);
      $Qzone->bindValue(':zone_code', $data['code']);
      $Qzone->bindInt(':zone_country_id', $data['country_id']);
      $Qzone->execute();

      return ( ($Qzone->rowCount() === 1) || !$Qzone->isError() );
    }
  }
?>
