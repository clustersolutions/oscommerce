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

  class ZoneFind {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $Qzones = $OSCOM_PDO->prepare('select * from :table_zones where zone_country_id = :zone_country_id and (zone_name like :zone_name or zone_code like :zone_code) order by zone_name');
      $Qzones->bindInt(':zone_country_id', $data['country_id']);
      $Qzones->bindValue(':zone_name', '%' . $data['keywords'] . '%');
      $Qzones->bindValue(':zone_code', '%' . $data['keywords'] . '%');
      $Qzones->execute();

      $result['entries'] = $Qzones->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
