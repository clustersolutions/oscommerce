<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\Microsoft\SqlServer;

  use osCommerce\OM\Core\Registry;

  class ZoneGetAll {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('MSSQL');

      $result = array();

      $Qzones = $OSCOM_Database->query('select * from :table_zones where zone_country_id = :zone_country_id order by zone_name');
      $Qzones->bindInt(':zone_country_id', $data['country_id']);
      $Qzones->execute();

      $result['entries'] = $Qzones->getAll();

      $result['total'] = count($result['entries']);

      $Qzones->freeResult();

      return $result;
    }
  }
?>
