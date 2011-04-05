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

  class getPackageContents {
    public static function execute() {
      $result = array('entries' => array());

      $phar_can_open = true;

      try {
        $phar = new Phar(OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/update.phar');
      } catch ( \Exception $e ) {
        $phar_can_open = false;

        trigger_error($e->getMessage());
      }

      if ( $phar_can_open === true ) {
        $update_pkg = array();

        foreach ( new RecursiveIteratorIterator($phar) as $iteration ) {
          if ( ($pos = strpos($iteration->getPathName(), 'update.phar')) !== false ) {
            $update_pkg[] = substr($iteration->getPathName(), $pos+12);
          }
        }

        natcasesort($update_pkg);

        $counter = 0;

        foreach ( $update_pkg as $file ) {
          if ( substr($file, 0, 14) == 'osCommerce/OM/' ) {
            $result['entries'][] = array('key' => $counter,
                                         'name' => $file,
                                         'exists' => file_exists(realpath(OSCOM::BASE_DIRECTORY . '/../../') . '/' . $file),
                                         'writable' => self::isWritable(realpath(OSCOM::BASE_DIRECTORY . '/../../') . '/' . $file));

            $counter++;
          } elseif ( substr($file, 0, 7) == 'public/' ) {
            $result['entries'][] = array('key' => $counter,
                                         'name' => $file,
                                         'exists' => file_exists(realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file),
                                         'writable' => self::isWritable(realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file));

            $counter++;
          }
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }

    public static function isWritable($location) {
      if ( !file_exists($location) ) {
        while ( true ) {
          $location = dirname($location);

          if ( file_exists($location) ) {
            break;
          }
        }
      }

      return is_writable($location);
    }
  }
?>
