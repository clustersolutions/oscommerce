<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Administrators\Model;

  use osCommerce\OM\Core\Site\Admin\Application\Administrators\Administrators;
  use osCommerce\OM\Core\OSCOM;

  class setAccessLevels {
    public static function execute($id, $modules, $mode = Administrators::ACCESS_MODE_ADD) {
      $data = array('id' => $id,
                    'modules' => $modules,
                    'mode' => $mode);

      if ( in_array('0', $data['modules']) ) {
        $data['modules'] = array('*');
      }

      return OSCOM::callDB('Admin\Administrators\SavePermissions', $data);
    }
  }
?>
