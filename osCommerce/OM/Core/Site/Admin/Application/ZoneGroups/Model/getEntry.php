<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\Model;

  use osCommerce\OM\Core\OSCOM;

  class getEntry {
    public static function execute($id, $key = null) {
      $data = array('id' => $id);

      $result = OSCOM::callDB('Admin\ZoneGroups\EntryGet', $data);

      if ( empty($result['countries_name']) ) {
        $result['countries_name'] = OSCOM::getDef('all_countries');
      }

      if ( empty($result['zone_name']) ) {
        $result['zone_name'] = OSCOM::getDef('all_zones');
      }

      if ( !empty($key) && isset($result[$key]) ) {
        $result = $result[$key];
      }

      return $result;
    }
  }
?>
