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

  class EntrySave {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      if ( isset($data['id']) && is_numeric($data['id']) ) {
        $Qrate = $OSCOM_Database->prepare('update :table_tax_rates set tax_zone_id = :tax_zone_id, tax_priority = :tax_priority, tax_rate = :tax_rate, tax_description = :tax_description, last_modified = now() where tax_rates_id = :tax_rates_id');
        $Qrate->bindInt(':tax_rates_id', $data['id']);
      } else {
        $Qrate = $OSCOM_Database->prepare('insert into :table_tax_rates (tax_zone_id, tax_class_id, tax_priority, tax_rate, tax_description, date_added) values (:tax_zone_id, :tax_class_id, :tax_priority, :tax_rate, :tax_description, now())');
        $Qrate->bindInt(':tax_class_id', $data['tax_class_id']);
      }

      $Qrate->bindInt(':tax_zone_id', $data['zone_id']);
      $Qrate->bindInt(':tax_priority', $data['priority']);
      $Qrate->bindValue(':tax_rate', $data['rate']);
      $Qrate->bindValue(':tax_description', $data['description']);
      $Qrate->execute();

      return ( ($Qrate->rowCount() === 1) || !$Qrate->isError() );
    }
  }
?>
