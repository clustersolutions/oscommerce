<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2013 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  use osCommerce\OM\Core\HTML;

  class post extends \osCommerce\OM\Core\Template\TagAbstract {
    static protected $_parse_result = false;

    static public function execute($string) {
      if ( strpos($string, '|') !== false ) {
        list($string, $default) = explode('|', $string, 2);
      }

      if ( isset($_POST[$string]) ) {
        return HTML::outputProtected($_POST[$string]);
      } elseif ( isset($default) ) {
        return value::execute($default);
      }

      return null;
    }
  }
?>
