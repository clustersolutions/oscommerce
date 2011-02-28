<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Index\Model;

  use osCommerce\OM\Core\OSCOM;

  class updateAppDateOpened {
    public static function execute($admin_id, $application) {
      $data = array('admin_id' => $admin_id,
                    'application' => $application);

      return OSCOM::callDB('Admin\Index\UpdateAppLastOpened', $data);
    }
  }
?>
