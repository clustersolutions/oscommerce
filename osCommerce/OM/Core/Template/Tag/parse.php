<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2013 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  use osCommerce\OM\Core\Registry;

  class parse extends \osCommerce\OM\Core\Template\TagAbstract {
    static protected $_parse_result = false;

    static public function execute($string) {
      $OSCOM_Template = Registry::get('Template');

      $args = func_get_args();

      $whitelist = null;

      if ( isset($args[1]) ) {
        $whitelist_string = trim($args[1]);

        if ( !empty($whitelist_string) ) {
          $whitelist = explode(' ', $whitelist_string);
        }
      }

      return $OSCOM_Template->parseContent($string, $whitelist);
    }
  }
?>
