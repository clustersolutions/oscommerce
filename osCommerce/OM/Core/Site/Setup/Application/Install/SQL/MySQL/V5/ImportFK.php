<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Setup\Application\Install\SQL\MySQL\V5;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class ImportFK {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $sql_file = OSCOM::BASE_DIRECTORY . 'Core/Site/Setup/sql/oscommerce_innodb.sql';

      return $OSCOM_PDO->importSQL($sql_file, $data['table_prefix']);
    }
  }
?>
