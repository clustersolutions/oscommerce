<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Model;

  use \Phar;
  use osCommerce\OM\Core\OSCOM;

  class getPackageInfo {
    public static function execute($key = null) {
      $phar_can_open = true;

      try {
        $phar = new Phar(OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/update.phar');
      } catch ( \Exception $e ) {
        $phar_can_open = false;

        trigger_error($e->getMessage());
      }

      if ( $phar_can_open === true ) {
        $result = $phar->getMetadata();

        if ( !empty($key) && isset($result[$key]) ) {
          $result = $result[$key];
        }

        return $result;
      }

      return false;
    }
  }
?>
