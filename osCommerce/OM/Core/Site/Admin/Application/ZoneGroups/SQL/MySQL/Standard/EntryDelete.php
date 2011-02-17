<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class EntryDelete {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $Qentry = $OSCOM_Database->prepare('delete from :table_zones_to_geo_zones where association_id = :association_id');
      $Qentry->bindInt(':association_id', $data['id']);
      $Qentry->execute();

      return ( $Qentry->rowCount() === 1 );
    }
  }
?>
