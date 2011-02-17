<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Save {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      if ( isset($data['id']) && is_numeric($data['id']) ) {
        $Qzone = $OSCOM_Database->prepare('update :table_geo_zones set geo_zone_name = :geo_zone_name, geo_zone_description = :geo_zone_description, last_modified = now() where geo_zone_id = :geo_zone_id');
        $Qzone->bindInt(':geo_zone_id', $data['id']);
      } else {
        $Qzone = $OSCOM_Database->prepare('insert into :table_geo_zones (geo_zone_name, geo_zone_description, date_added) values (:geo_zone_name, :geo_zone_description, now())');
      }

      $Qzone->bindValue(':geo_zone_name', $data['zone_name']);
      $Qzone->bindValue(':geo_zone_description', $data['zone_description']);
      $Qzone->execute();

      return ( ($Qzone->rowCount() === 1) || !$Qzone->isError() );
    }
  }
?>
