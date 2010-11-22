<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Countries {
    public static function get($id, $key = null) {
      $data = array('id' => $id);

      $result = OSCOM::callDB('Get', $data);

      if ( !empty($key) && isset($result[$key]) ) {
        $result = $result[$key];
      }

      return $result;
    }

    public static function getAll($pageset = 1) {
      $data = array('batch_pageset' => $pageset,
                    'batch_max_results' => MAX_DISPLAY_SEARCH_RESULTS);

      if ( !is_numeric($data['batch_pageset']) || (floor($data['batch_pageset']) != $data['batch_pageset']) ) {
        $data['batch_pageset'] = 1;
      }

      return OSCOM::callDB('GetAll', $data);
    }

    public static function find($search, $pageset = 1) {
      $data = array('keywords' => $search,
                    'batch_pageset' => $pageset,
                    'batch_max_results' => MAX_DISPLAY_SEARCH_RESULTS);

      if ( !is_numeric($data['batch_pageset']) || (floor($data['batch_pageset']) != $data['batch_pageset']) ) {
        $data['batch_pageset'] = 1;
      }

      return OSCOM::callDB('Find', $data);
    }

    public static function findZones($search, $country_id) {
      $data = array('keywords' => $search,
                    'country_id' => $country_id);

      return OSCOM::callDB('ZoneFind', $data);
    }

    public static function getZone($id) {
      $data = array('id' => $id);

      return OSCOM::callDB('ZoneGet', $data);
    }

    public static function getAllZones($country_id) {
      $data = array('country_id' => $country_id);

      return OSCOM::callDB('ZoneGetAll', $data);
    }

    public static function save($id = null, $data) {
      if ( is_numeric($id) ) {
        $data['id'] = $id;
      }

      return OSCOM::callDB('Save', $data);
    }

    public static function delete($id) {
      $data = array('id' => $id);

      return OSCOM::callDB('Delete', $data);
    }

    public static function saveZone($id = null, $data) {
      if ( is_numeric($id) ) {
        $data['id'] = $id;
      }

      return OSCOM::callDB('ZoneSave', $data);
    }

    public static function deleteZone($id) {
      $data = array('id' => $id);

      return OSCOM::callDB('ZoneDelete', $data);
    }
  }
?>
