<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  class import extends \osCommerce\OM\Core\Template\TagAbstract {
    static public function execute($string) {
      if ( file_exists($string) ) {
        return file_get_contents($string);
      } else {
        trigger_error('Template Tag {import}: File does not exist: ' . $string);
      }

      return false;
    }
  }
?>
