<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
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
