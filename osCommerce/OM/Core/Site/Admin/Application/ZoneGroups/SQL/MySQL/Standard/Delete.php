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

  class Delete {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $Qzone = $OSCOM_Database->prepare('delete from :table_geo_zones where geo_zone_id = :geo_zone_id');
      $Qzone->bindInt(':geo_zone_id', $data['id']);
      $Qzone->execute();

      return ( $Qzone->rowCount() === 1 );
    }
  }
?>
