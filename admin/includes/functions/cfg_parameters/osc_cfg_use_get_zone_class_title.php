<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  function osc_cfg_use_get_zone_class_title($id) {
    global $osC_Database;

    if ($id == '0') {
      return TEXT_NONE;
    }

    $Qclass = $osC_Database->query('select geo_zone_name from :table_geo_zones where geo_zone_id = :geo_zone_id');
    $Qclass->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
    $Qclass->bindInt(':geo_zone_id', $id);
    $Qclass->execute();

    return $Qclass->value('geo_zone_name');
  }
?>
