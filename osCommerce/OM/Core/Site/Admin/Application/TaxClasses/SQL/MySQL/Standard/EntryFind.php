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

  class EntryFind {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $result = array();

      $Qrates = $OSCOM_Database->prepare('select tr.*, z.geo_zone_id, z.geo_zone_name from :table_tax_rates tr, :table_geo_zones z where tr.tax_class_id = :tax_class_id and tr.tax_zone_id = z.geo_zone_id and (tr.tax_description like :tax_description) order by tr.tax_priority, z.geo_zone_name');
      $Qrates->bindInt(':tax_class_id', $data['tax_class_id']);
      $Qrates->bindValue(':tax_description', '%' . $data['keywords'] . '%');
      $Qrates->execute();

      $result['entries'] = $Qrates->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
