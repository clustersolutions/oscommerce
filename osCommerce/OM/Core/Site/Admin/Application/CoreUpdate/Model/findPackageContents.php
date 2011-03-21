<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Model;

  use osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\CoreUpdate;

  class findPackageContents {
    public static function execute($search) {
      $result = CoreUpdate::getPackageContents();

      foreach ( $result['entries'] as $k => $v ) {
        if ( strpos($v['name'], $search) === false ) {
          unset($result['entries'][$k]);
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
