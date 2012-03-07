<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

  abstract class TemplateAbstract {
    static protected $_parent;
    static protected $_base_filename = 'base.html';

    static public function getBaseFilename() {
      return static::$_base_filename;
    }

    static public function hasParent() {
      return isset(static::$_parent);
    }

    static public function getParent() {
      return static::$_parent;
    }
  }
?>
