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

  class DeleteExpired {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qsession = $OSCOM_PDO->prepare('delete from :table_sessions where expiry < :expiry');
      $Qsession->bindInt(':expiry', $data['expiry']);
      $Qsession->execute();

      return ( $Qsession->rowCount() > 0 );
    }
  }
?>
