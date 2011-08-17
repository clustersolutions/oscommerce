<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ProductAttributes\Model;

  use osCommerce\OM\Core\Site\Admin\Application\ProductAttributes\ProductAttributes;

/**
 * @since v3.0.3
 */

  class findInstalled {
    public static function execute($search) {
      $modules = ProductAttributes::getInstalled();

      $result = array('entries' => array());

      foreach ( $modules['entries'] as $module ) {
        if ( (stripos($module['code'], $search) !== false) || (stripos($module['title'], $search) !== false) ) {
          $result['entries'][] = $module;
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
