<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  use osCommerce\OM\Core\Registry;

  class loop extends \osCommerce\OM\Core\Template\TagAbstract {
    static protected $_parse_result = false;

    static public function execute($string) {
      $args = func_get_args();

      $OSCOM_Template = Registry::get('Template');

      $data = $OSCOM_Template->getValue(trim($args[1]));

      $result = '';

      if ( !empty($data) ) {
        foreach ( $data as $d ) {
          $result .= preg_replace_callback('/#(.*?)\b#/', function ($matches) use (&$d) {
                       return ( isset($d[$matches[1]]) ? $d[$matches[1]] : $matches[0] );
                     }, $string);
        }
      }

      return $result;
    }
  }
?>
