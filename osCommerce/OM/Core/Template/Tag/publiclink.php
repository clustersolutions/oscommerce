<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  use osCommerce\OM\Core\OSCOM;

  class publiclink extends \osCommerce\OM\Core\Template\TagAbstract {
    static protected $_parse_result = false;

    static public function execute($string) {
      $params = explode('|', $string, 3);

      if ( !isset($params[1]) || empty($params[1]) ) {
        $params[1] = null;
      }

      if ( !isset($params[2]) || empty($params[2]) ) {
        $params[2] = null;
      }

      return OSCOM::getPublicSiteLink($params[0], $params[2], $params[1]);
    }
  }
?>
