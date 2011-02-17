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

  class Delete {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $Qdel = $OSCOM_Database->prepare('delete from :table_administrators where id = :id');
      $Qdel->bindInt(':id', $data['id']);
      $Qdel->execute();

      return ( $Qdel->rowCount() === 1 );
    }
  }
?>
