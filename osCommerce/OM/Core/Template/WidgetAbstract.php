<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template;

  use osCommerce\OM\Core\Registry;

  abstract class WidgetAbstract {
    static public function initialize() {
      $widget = array_slice(explode('\\', get_called_class()), -2, 1);

      Registry::get('Language')->loadIniFile('Modules/Template/Widgets/' . $widget[0] . '.php');

      return static::execute();
    }

/**
 * Not declared as an abstract static function as it freaks PHP 5.3 out
 */

    static public function execute() {
      return false;
    }
  }
?>
