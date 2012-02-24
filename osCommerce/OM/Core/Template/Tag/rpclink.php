<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  use osCommerce\OM\Core\OSCOM;

  class rpclink extends \osCommerce\OM\Core\Template\TagAbstract {
    static protected $_parse_result = false;

/**
 * @param string $string params|application|site
 */

    static public function execute($string) {
      $params = explode('|', $string, 3);

      if ( !isset($params[1]) ) {
        $params[1] = null;
      }

      if ( !isset($params[2]) ) {
        $params[2] = null;
      }

      return OSCOM::getRPCLink($params[2], $params[1], $params[0]);
    }
  }
?>
