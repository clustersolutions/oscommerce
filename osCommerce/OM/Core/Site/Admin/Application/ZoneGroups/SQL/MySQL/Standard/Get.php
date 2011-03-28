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

  class Get {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qzones = $OSCOM_PDO->prepare('select gz.*, count(z2gz.association_id) as total_entries from :table_geo_zones gz left join :table_zones_to_geo_zones z2gz on (gz.geo_zone_id = z2gz.geo_zone_id) where gz.geo_zone_id = :geo_zone_id');
      $Qzones->bindInt(':geo_zone_id', $data['id']);
      $Qzones->execute();

      return $Qzones->fetch();
    }
  }
?>
