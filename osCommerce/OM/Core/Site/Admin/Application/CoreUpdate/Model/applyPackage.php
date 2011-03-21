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

  class applyPackage {
    public static function execute() {
      $phar_can_open = true;

      try {
        $phar = new Phar(OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/update.phar');
        $phar->extractTo(realpath(OSCOM::BASE_DIRECTORY . '../../'), null, true);
      } catch ( \Exception $e ) {
// ignore when file permissions from the phar archive cannot be set to the
// extracted files
// HPDL look for a more elegant solution
        if ( strpos($e->getMessage(), 'setting file permissions failed') === false ) {
          $phar_can_open = false;

          trigger_error($e->getMessage());
        }
      }

      return $phar_can_open;
    }
  }
?>
