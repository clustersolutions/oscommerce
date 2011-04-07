<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Services\Model;

  class exists {
    public static function execute($code) {
      return class_exists('osCommerce\\OM\\Core\\Site\\Admin\\Module\\Service\\' . $code);
    }
  }
?>
