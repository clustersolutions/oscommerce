<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2013 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  class ul extends \osCommerce\OM\Core\Template\TagAbstract {
    static public function execute($string) {
      return '<ul><li>' . str_replace('|', '</li><li>', $string) . '</li></ul>';
    }
  }
?>
