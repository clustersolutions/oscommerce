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
  use osCommerce\OM\Core\Registry;

  class loop extends \osCommerce\OM\Core\Template\TagAbstract {
    static protected $_parse_result = false;

    static public function execute($string) {
      $args = func_get_args();

      $OSCOM_Template = Registry::get('Template');

      $key = trim($args[1]);

      if ( !$OSCOM_Template->valueExists($key) ) {
        if ( class_exists('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Module\\Template\\Value\\' . $key . '\\Controller') && is_subclass_of('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Module\\Template\\Value\\' . $key . '\\Controller', 'osCommerce\\OM\\Core\\Template\\ValueAbstract') ) {
          call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Module\\Template\\Value\\' . $key . '\\Controller', 'initialize'));
        } elseif ( class_exists('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Value\\' . $key . '\\Controller') && is_subclass_of('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Value\\' . $key . '\\Controller', 'osCommerce\\OM\\Core\\Template\\ValueAbstract') ) {
          call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Value\\' . $key . '\\Controller', 'initialize'));
        }
      }

      $data = $OSCOM_Template->getValue($key);

      $result = '';

      if ( !empty($data) ) {
        foreach ( $data as $d ) {
          $result .= preg_replace_callback('/[#|%]([a-zA-Z0-9_-]+)[#|%]/', function ($matches) use (&$d) {
                       if ( substr($matches[0], 0, 1) == '%' ) {
                         return ( isset($d[$matches[1]]) ? $d[$matches[1]] : $matches[0] );
                       } else {
                         return ( isset($d[$matches[1]]) ? HTML::outputProtected($d[$matches[1]]) : $matches[0] );
                       }
                     }, $string);
        }
      }

      return $result;
    }
  }
?>
