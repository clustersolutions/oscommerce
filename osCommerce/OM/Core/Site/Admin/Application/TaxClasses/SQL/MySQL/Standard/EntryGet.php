<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\TaxClasses\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class EntryGet {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qrates = $OSCOM_PDO->prepare('select tr.*, tc.tax_class_title, z.geo_zone_id, z.geo_zone_name from :table_tax_rates tr, :table_tax_class tc, :table_geo_zones z where tr.tax_rates_id = :tax_rates_id and tr.tax_class_id = tc.tax_class_id and tr.tax_zone_id = z.geo_zone_id');
      $Qrates->bindInt(':tax_rates_id', $data['id']);
      $Qrates->execute();

      return $Qrates->fetch();
    }
  }
?>
