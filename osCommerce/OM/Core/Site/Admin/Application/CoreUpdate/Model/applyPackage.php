<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Model;

  use \Phar;
  use \RecursiveIteratorIterator;
  use osCommerce\OM\Core\OSCOM;

  class applyPackage {
    public static function execute() {
      $phar_can_open = true;

      try {
        $phar = new Phar(OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/update.phar');

// loop through each file individually as extractTo() does not work with
// directories (see http://bugs.php.net/bug.php?id=54289)
        foreach ( new RecursiveIteratorIterator($phar) as $iteration ) {
          if ( ($pos = strpos($iteration->getPathName(), 'update.phar')) !== false ) {
            $file = substr($iteration->getPathName(), $pos+12);

            if ( substr($file, 0, 14) == 'osCommerce/OM/' ) {
              $phar->extractTo(realpath(OSCOM::BASE_DIRECTORY . '../../'), $file, true);
            } elseif ( substr($file, 0, 7) == 'public/' ) {
              $phar->extractTo(realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../'), $file, true);
            }
          }
        }
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
