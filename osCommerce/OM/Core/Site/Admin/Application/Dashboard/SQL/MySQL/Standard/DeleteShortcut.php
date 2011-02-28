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

  class DeleteShortcut {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $Qsc = $OSCOM_Database->prepare('delete from :table_administrator_shortcuts where administrators_id = :administrators_id and module = :module');
      $Qsc->bindInt(':administrators_id', $data['admin_id']);
      $Qsc->bindValue(':module', $data['application']);
      $Qsc->execute();

      return ( ($Qsc->rowCount() === 1) || !$Qsc->isError() );
    }
  }
?>
