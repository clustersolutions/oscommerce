<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  use osCommerce\OM\Core\Registry;

  class iftrue extends \osCommerce\OM\Core\Template\TagAbstract {
    static protected $_parse_result = false;

    static public function execute($string) {
      $args = func_get_args();

      $OSCOM_Template = Registry::get('Template');

      $result = '';

      if ( $OSCOM_Template->valueExists(trim($args[1])) ) {
        $data = $OSCOM_Template->getValue(trim($args[1]));

        if ( $data === true ) {
          $result = $string;
        }
      }

      return $result;
    }
  }
?>
