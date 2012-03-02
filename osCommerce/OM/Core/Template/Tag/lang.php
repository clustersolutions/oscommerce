<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  use osCommerce\OM\Core\OSCOM;

  class lang extends \osCommerce\OM\Core\Template\TagAbstract {
    static public function execute($string) {
      $args = func_get_args();

      if ( isset($args[1]) && !empty($args[1]) ) {
        return call_user_func(trim($args[1]), OSCOM::getDef($string));
      } else {
        return OSCOM::getDef($string);
      }
    }
  }
?>
