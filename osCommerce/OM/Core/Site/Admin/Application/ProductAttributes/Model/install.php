<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ProductAttributes\Model;

/**
 * @since v3.0.3
 */

  class install {
    public static function execute($module) {
      $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\ProductAttribute\\' . $module;

      if ( class_exists($class) ) {
        $OSCOM_PA = new $class();
        $OSCOM_PA->install();

        return true;
      }

      return false;
    }
  }
?>
