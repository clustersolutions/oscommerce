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

  class Save {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      if ( isset($data['id']) && is_numeric($data['id']) ) {
        $Qclass = $OSCOM_PDO->prepare('update :table_tax_class set tax_class_title = :tax_class_title, tax_class_description = :tax_class_description, last_modified = now() where tax_class_id = :tax_class_id');
        $Qclass->bindInt(':tax_class_id', $data['id']);
      } else {
        $Qclass = $OSCOM_PDO->prepare('insert into :table_tax_class (tax_class_title, tax_class_description, date_added) values (:tax_class_title, :tax_class_description, now())');
      }

      $Qclass->bindValue(':tax_class_title', $data['title']);
      $Qclass->bindValue(':tax_class_description', $data['description']);
      $Qclass->execute();

      return ( ($Qclass->rowCount() === 1) || !$Qclass->isError() );
    }
  }
?>
