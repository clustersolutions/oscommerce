<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;

  class delete {
    public static function execute($id) {
      $data = array('id' => $id);

      if ( (Languages::get($id, 'code') != DEFAULT_LANGUAGE) && OSCOM::callDB('Admin\Languages\Delete', $data) ) {
        Cache::clear('languages');

        return true;
      }

      return false;
    }
  }
?>
