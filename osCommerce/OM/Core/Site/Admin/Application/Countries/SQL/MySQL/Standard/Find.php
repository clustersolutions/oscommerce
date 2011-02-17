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

  class Find {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $result = array();

      $sql_query = 'select SQL_CALC_FOUND_ROWS c.*, count(z.zone_id) as total_zones from :table_countries c left join :table_zones z on (c.countries_id = z.zone_country_id) where (c.countries_name like :countries_name or c.countries_iso_code_2 like :countries_iso_code_2 or c.countries_iso_code_3 like :countries_iso_code_3 or z.zone_name like :zone_name or z.zone_code like :zone_code) group by c.countries_id order by c.countries_name';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_pageset, :batch_max_results';
      }

      $sql_query .= '; select found_rows();';

      $Qcountries = $OSCOM_Database->prepare($sql_query);
      $Qcountries->bindValue(':countries_name', '%' . $data['keywords'] . '%');
      $Qcountries->bindValue(':countries_iso_code_2', '%' . $data['keywords'] . '%');
      $Qcountries->bindValue(':countries_iso_code_3', '%' . $data['keywords'] . '%');
      $Qcountries->bindValue(':zone_name', '%' . $data['keywords'] . '%');
      $Qcountries->bindValue(':zone_code', '%' . $data['keywords'] . '%');

      if ( $data['batch_pageset'] !== -1 ) {
        $Qcountries->bindInt(':batch_pageset', $OSCOM_Database->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qcountries->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qcountries->execute();

      $result['entries'] = $Qcountries->fetchAll();

      $Qcountries->nextRowset();

      $result['total'] = $Qcountries->fetchColumn();

      return $result;
    }
  }
?>
