<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ProductAttributes\Model;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

/**
 * @since v3.0.3
 */

  class getInstalled {
    public static function execute() {
      $result = OSCOM::callDB('Admin\ProductAttributes\GetAll');

      foreach ( $result['entries'] as &$module ) {
        $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\ProductAttribute\\' . $module['code'];

        $OSCOM_PA = new $class();

        $module['title'] = $OSCOM_PA->getTitle();
      }

      return $result;
    }
  }
?>
