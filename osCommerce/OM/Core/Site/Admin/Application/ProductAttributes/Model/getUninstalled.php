<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ProductAttributes\Model;

  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\Application\ProductAttributes\ProductAttributes;

/**
 * @since v3.0.3
 */

  class getUninstalled {
    public static function execute() {
      $installed_modules = ProductAttributes::getInstalled();
      $installed = array();

      foreach ( $installed_modules['entries'] as $module ) {
        $installed[] = $module['code'];
      }

      $result = array('entries' => array());

      $DLpa = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Module/ProductAttribute');
      $DLpa->setIncludeDirectories(false);

      foreach ( $DLpa->getFiles() as $file ) {
        $module = substr($file['name'], 0, strrpos($file['name'], '.'));

        if ( !in_array($module, $installed) ) {
          $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\ProductAttribute\\' . $module;

          $OSCOM_PA = new $class();

          $result['entries'][] = array('code' => $OSCOM_PA->getCode(),
                                       'title' => $OSCOM_PA->getTitle());
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
