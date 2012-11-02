<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class ifvalue extends \osCommerce\OM\Core\Template\TagAbstract {
    static protected $_parse_result = false;

    static public function execute($string) {
      $args = func_get_args();

      $OSCOM_Template = Registry::get('Template');

      $key = trim($args[1]);

      if ( strpos($key, ' ') !== false ) {
        list($key, $entry) = explode(' ', $key, 2);
      }

      $result = '';

      if ( !$OSCOM_Template->valueExists($key) ) {
        if ( class_exists('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Module\\Template\\Value\\' . $key . '\\Controller') && is_subclass_of('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Module\\Template\\Value\\' . $key . '\\Controller', 'osCommerce\\OM\\Core\\Template\\ValueAbstract') ) {
          call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Module\\Template\\Value\\' . $key . '\\Controller', 'initialize'));
        } elseif ( class_exists('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Value\\' . $key . '\\Controller') && is_subclass_of('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Value\\' . $key . '\\Controller', 'osCommerce\\OM\\Core\\Template\\ValueAbstract') ) {
          call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Value\\' . $key . '\\Controller', 'initialize'));
        }
      }

      if ( $OSCOM_Template->valueExists($key) ) {
        $value = $OSCOM_Template->getValue($key);

        if ( isset($entry) ) {
          if ( isset($value[$entry]) ) {
            $value = $value[$entry];
          } else {
            unset($value);
          }
        }

        if ( isset($value) && ((is_string($value) && (strlen($value) > 0)) || (is_array($value) && (count($value) > 0))) ) {
          $result = $string;
        }
      }

      return $result;
    }
  }
?>
