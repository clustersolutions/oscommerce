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

  class GetTotalProducts {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qtotal = $OSCOM_PDO->prepare('select count(*) as total from :table_products where products_tax_class_id = :products_tax_class_id');
      $Qtotal->bindInt(':products_tax_class_id', $data['id']);
      $Qtotal->execute();

      $result = $Qtotal->fetch();

      return $result['total'];
    }
  }
?>
