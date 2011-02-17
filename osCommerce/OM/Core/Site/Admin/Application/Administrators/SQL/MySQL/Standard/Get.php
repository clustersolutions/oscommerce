<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Administrators\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Get {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $Qadmin = $OSCOM_Database->prepare('select * from :table_administrators where id = :id');
      $Qadmin->bindInt(':id', $data['id']);
      $Qadmin->execute();

      $result = $Qadmin->fetch();

      $result['access_modules'] = array();

      $Qaccess = $OSCOM_Database->prepare('select module from :table_administrators_access where administrators_id = :administrators_id');
      $Qaccess->bindInt(':administrators_id', $data['id']);
      $Qaccess->execute();

      while ( $row = $Qaccess->fetch() ) {
        $result['access_modules'][] = $row['module'];
      }

      return $result;
    }
  }
?>
