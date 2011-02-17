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

  class GetTotalTaxRates {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $Qtotal = $OSCOM_Database->prepare('select count(*) as total from :table_tax_rates where tax_zone_id = :tax_zone_id');
      $Qtotal->bindInt(':tax_zone_id', $data['tax_zone_id']);
      $Qtotal->execute();

      $result = $Qtotal->fetch();

      return $result['total'];
    }
  }
?>
