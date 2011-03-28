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

  class EntryGet {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qentries = $OSCOM_PDO->prepare('select z2gz.*, c.countries_name, z.zone_name from :table_zones_to_geo_zones z2gz left join :table_countries c on (z2gz.zone_country_id = c.countries_id) left join :table_zones z on (z2gz.zone_id = z.zone_id) where z2gz.association_id = :association_id');
      $Qentries->bindInt(':association_id', $data['id']);
      $Qentries->execute();

      return $Qentries->fetch();
    }
  }
?>
