<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Index\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class UpdateAppLastOpened {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $Qreset = $OSCOM_Database->prepare('update :table_administrator_shortcuts set last_viewed = now() where administrators_id = :administrators_id and module = :module');
      $Qreset->bindInt(':administrators_id', $data['admin_id']);
      $Qreset->bindValue(':module', $data['application']);
      $Qreset->execute();

      return ( ($Qreset->rowCount() === 1) || !$Qreset->isError() );
    }
  }
?>
