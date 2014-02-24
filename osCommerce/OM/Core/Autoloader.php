<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2014 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

  define('OSCOM_BASE_DIRECTORY', realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR);

  class Autoloader {
    public static function load($class) {
      $prefix = 'osCommerce\\OM\\';

// only auto load related classes
      $len = strlen($prefix);
      if ( strncmp($prefix, $class, $len) !== 0 ) {
        return false;
      }

      $class = substr($class, $len);

      $file = OSCOM_BASE_DIRECTORY . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
      $custom = str_replace('osCommerce' . DIRECTORY_SEPARATOR . 'OM' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR, 'osCommerce' . DIRECTORY_SEPARATOR . 'OM' . DIRECTORY_SEPARATOR . 'Custom' . DIRECTORY_SEPARATOR, $file);

      if ( file_exists($custom) ) {
        require($custom);
      } else if ( file_exists($file) ) {
        require($file);
      }
    }
  }
?>
