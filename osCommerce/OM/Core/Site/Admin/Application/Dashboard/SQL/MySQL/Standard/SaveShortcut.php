<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Dashboard\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class SaveShortcut {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $Qsc = $OSCOM_Database->prepare('insert into :table_administrator_shortcuts (administrators_id, module, last_viewed) values (:administrators_id, :module, null)');
      $Qsc->bindInt(':administrators_id', $data['admin_id']);
      $Qsc->bindValue(':module', $data['application']);
      $Qsc->execute();

      return ( ($Qsc->rowCount() === 1) || !$Qsc->isError() );
    }
  }
?>
