<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template;

  abstract class TagAbstract {
    static protected $_parse_result = true;

/**
 * Not declared as an abstract static function as it freaks PHP 5.3 out
 */

    static public function execute($string) {
      return $string;
    }

    static public function parseResult() {
      return static::$_parse_result;
    }
  }
?>
