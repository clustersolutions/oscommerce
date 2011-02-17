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

  class EntryDelete {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $Qrate = $OSCOM_Database->prepare('delete from :table_tax_rates where tax_rates_id = :tax_rates_id');
      $Qrate->bindInt(':tax_rates_id', $data['id']);
      $Qrate->execute();

      return ( $Qrate->rowCount() === 1 );
    }
  }
?>
