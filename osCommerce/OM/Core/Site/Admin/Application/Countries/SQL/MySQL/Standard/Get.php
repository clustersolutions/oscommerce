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

  class Get {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('Database');

      $Qcountries = $OSCOM_Database->query('select * from :table_countries where countries_id = :countries_id');
      $Qcountries->bindInt(':countries_id', $data['id']);
      $Qcountries->execute();

      $Qzones = $OSCOM_Database->query('select count(*) as total_zones from :table_zones where zone_country_id = :zone_country_id');
      $Qzones->bindInt(':zone_country_id', $data['id']);
      $Qzones->execute();

      return array_merge($Qcountries->toArray(), $Qzones->toArray());
    }
  }
?>
