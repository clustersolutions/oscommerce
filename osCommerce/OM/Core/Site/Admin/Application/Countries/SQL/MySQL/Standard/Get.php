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
      $OSCOM_Database = Registry::get('PDO');

      $Qcountries = $OSCOM_Database->prepare('select c.*, count(z.zone_id) as total_zones2 from :table_countries c left join :table_zones z on (c.countries_id = z.zone_country_id) where c.countries_id = :countries_id');
      $Qcountries->bindInt(':countries_id', $data['id']);
      $Qcountries->execute();

      return $Qcountries->fetch();
    }
  }
?>
