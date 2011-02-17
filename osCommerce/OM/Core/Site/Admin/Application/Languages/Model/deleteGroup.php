<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;

  class deleteGroup {
    public static function execute($group) {
      $data = array('group' => $group);

      if ( OSCOM::callDB('Admin\Languages\DeleteGroup', $data) ) {
        Cache::clear('languages');

        return true;
      }

      return false;
    }
  }
?>
