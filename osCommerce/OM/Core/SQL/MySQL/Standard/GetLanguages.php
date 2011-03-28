<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetLanguages {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qlanguages = $OSCOM_PDO->query('select * from :table_languages order by sort_order, name');
// HPDL     $Qlanguages->setCache('languages');
      $Qlanguages->execute();

      return $Qlanguages->fetchAll();
    }
  }
?>
