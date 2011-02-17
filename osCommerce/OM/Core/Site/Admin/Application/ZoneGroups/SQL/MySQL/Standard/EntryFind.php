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

  class EntryFind {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $result = array();

      $Qentries = $OSCOM_Database->prepare('select z2gz.*, c.countries_name, z.zone_name from :table_zones_to_geo_zones z2gz, :table_countries c, :table_zones z where z2gz.geo_zone_id = :geo_zone_id and z2gz.zone_country_id = c.countries_id and z2gz.zone_id = z.zone_id and (c.countries_name like :countries_name or z.zone_name like :zone_name) order by c.countries_name, z.zone_name');
      $Qentries->bindInt(':geo_zone_id', $data['group_id']);
      $Qentries->bindValue(':countries_name', '%' . $data['keywords'] . '%');
      $Qentries->bindValue(':zone_name', '%' . $data['keywords'] . '%');
      $Qentries->execute();

      $result['entries'] = $Qentries->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
