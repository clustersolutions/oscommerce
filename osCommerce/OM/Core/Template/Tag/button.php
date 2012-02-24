<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  class button extends \osCommerce\OM\Core\Template\TagAbstract {
    static protected $_parse_result = false;

    static public function execute($string) {
      $params = explode('|', $string, 2);

      $data = array('icon' => $params[0]);

      if ( isset($params[1]) ) {
        $data['title'] = OSCOM::getDef($params[1]);
      }

      return HTML::button($data);
    }
  }
?>
