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

  class EntrySave {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      if ( isset($data['id']) && is_numeric($data['id']) ) {
        $Qentry = $OSCOM_Database->prepare('update :table_zones_to_geo_zones set zone_country_id = :zone_country_id, zone_id = :zone_id, last_modified = now() where association_id = :association_id');
        $Qentry->bindInt(':association_id', $data['id']);
      } else {
        $Qentry = $OSCOM_Database->prepare('insert into :table_zones_to_geo_zones (zone_country_id, zone_id, geo_zone_id, date_added) values (:zone_country_id, :zone_id, :geo_zone_id, now())');
        $Qentry->bindInt(':geo_zone_id', $data['group_id']);
      }

      $Qentry->bindInt(':zone_country_id', $data['country_id']);
      $Qentry->bindInt(':zone_id', $data['zone_id']);
      $Qentry->execute();

      return ( ($Qentry->rowCount() === 1) || !$Qentry->isError() );
    }
  }
?>
