<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Session\Database\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Get {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $sql_query = 'select value from :table_sessions where id = :id';

      if ( isset($data['expiry']) ) {
        $sql_query .= ' and expiry >= :expiry';
      }

      $Qsession = $OSCOM_Database->prepare($sql_query);
      $Qsession->bindValue(':id', $data['id']);

      if ( isset($data['expiry']) ) {
        $Qsession->bindInt(':expiry', $data['expiry']);
      }

      $Qsession->execute();

      return $Qsession->fetch();
    }
  }
?>
