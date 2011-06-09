<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Services\Model;

  use osCommerce\OM\Core\Cache;
  use osCommerce\OM\Core\OSCOM;

  class uninstall {
    public static function execute($module) {
      $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Service\\' . $module;

      if ( class_exists($class) ) {
        $OSCOM_SM = new $class();
        $OSCOM_SM->remove();

        $sm = explode(';', MODULE_SERVICES_INSTALLED);

        unset($sm[array_search($module, $sm)]);

        $data = array('key' => 'MODULE_SERVICES_INSTALLED',
                      'value' => implode(';', $sm));

        if ( OSCOM::callDB('Admin\Configuration\EntrySave', $data) ) {
          Cache::clear('configuration');

          return true;
        }
      }

      return false;
    }
  }
?>
