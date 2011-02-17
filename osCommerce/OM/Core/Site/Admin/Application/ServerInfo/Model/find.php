<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\ServerInfo\Model;

  use osCommerce\OM\Core\Site\Admin\Application\ServerInfo\ServerInfo;

  class find {
    public static function execute($search) {
      $modules = ServerInfo::getAll();

      $result = array('entries' => array());

      foreach ( $modules['entries'] as $module ) {
        if ( (stripos($module['key'], $search) !== false) || (stripos($module['title'], $search) !== false) || (stripos($module['value'], $search) !== false) ) {
          $result['entries'][] = $module;
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
