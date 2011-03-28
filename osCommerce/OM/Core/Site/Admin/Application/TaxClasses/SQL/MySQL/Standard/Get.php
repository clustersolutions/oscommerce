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

  class Get {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qclasses = $OSCOM_PDO->prepare('select tc.*, count(tr.tax_rates_id) as total_tax_rates from :table_tax_class tc left join :table_tax_rates tr on (tc.tax_class_id = tr.tax_class_id) where tc.tax_class_id = :tax_class_id');
      $Qclasses->bindInt(':tax_class_id', $data['id']);
      $Qclasses->execute();

      return $Qclasses->fetch();
    }
  }
?>
