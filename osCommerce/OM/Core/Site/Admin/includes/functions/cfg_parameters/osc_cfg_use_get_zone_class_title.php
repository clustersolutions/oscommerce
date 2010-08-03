<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  function osc_cfg_use_get_zone_class_title($id) {
    $OSCOM_Database = Registry::get('Database');
    $OSCOM_Language = Registry::get('Language');

    if ( $id == '0' ) {
      return OSCOM::getDef('parameter_none');
    }

    $Qclass = $OSCOM_Database->query('select geo_zone_name from :table_geo_zones where geo_zone_id = :geo_zone_id');
    $Qclass->bindInt(':geo_zone_id', $id);
    $Qclass->execute();

    return $Qclass->value('geo_zone_name');
  }
?>
