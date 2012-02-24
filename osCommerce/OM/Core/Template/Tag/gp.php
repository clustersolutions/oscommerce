<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  use osCommerce\OM\Core\HTML;

  class gp extends \osCommerce\OM\Core\Template\TagAbstract {
    static protected $_parse_result = false;

    static public function execute($string) {
      if ( isset($_GET[$string]) ) {
        return HTML::output($_GET[$string]);
      } elseif ( isset($_POST[$string]) ) {
        return HTML::output($_POST[$string]);
      }

      return null;
    }
  }
?>
