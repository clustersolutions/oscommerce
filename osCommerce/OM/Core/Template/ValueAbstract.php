<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template;

  use osCommerce\OM\Core\Registry;

  abstract class ValueAbstract {
    static public function initialize() {
      $OSCOM_Template = Registry::get('Template');

      $key = array_slice(explode('\\', get_called_class()), -2, 1);

      $OSCOM_Template->setValue($key[0], static::execute());
    }

/**
 * Not declared as an abstract static function as it freaks PHP 5.3 out
 */

    static public function execute() {
      return null;
    }
  }
?>
