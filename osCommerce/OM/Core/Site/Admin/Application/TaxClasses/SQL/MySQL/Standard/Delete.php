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

  class Delete {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $Qclass = $OSCOM_Database->prepare('delete from :table_tax_class where tax_class_id = :tax_class_id');
      $Qclass->bindInt(':tax_class_id', $data['id']);
      $Qclass->execute();

      return ( $Qclass->rowCount() === 1 );
    }
  }
?>
