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

  class GetAll {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('Database');

      $result = array();

      $Qcountries = $OSCOM_Database->query('select SQL_CALC_FOUND_ROWS c.*, count(z.zone_id) as total_zones from :table_countries c, :table_zones z where c.countries_id = z.zone_country_id group by c.countries_id order by c.countries_name');

      if ( $data['batch_pageset'] !== -1 ) {
        $Qcountries->setBatchLimit($data['batch_pageset'], $data['batch_max_results']);
      }

      $Qcountries->execute();

      $result['entries'] = $Qcountries->getAll();

      $result['total'] = $Qcountries->getBatchSize();

      return $result;
    }
  }
?>
