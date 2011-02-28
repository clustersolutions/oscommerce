<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Dashboard\Model;

  use osCommerce\OM\Core\OSCOM;

  class getShortcuts {
    public static function execute($admin_id) {
      $data = array('admin_id' => $admin_id);

      return OSCOM::callDB('Admin\Dashboard\GetShortcuts', $data);
    }
  }
?>
