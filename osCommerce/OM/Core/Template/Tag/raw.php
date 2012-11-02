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

  class raw extends \osCommerce\OM\Core\Template\TagAbstract {
    static protected $_parse_result = false;

    static public function execute($string) {
      $OSCOM_Template = Registry::get('Template');

      if ( !$OSCOM_Template->valueExists($string) ) {
        if ( class_exists('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Module\\Template\\Value\\' . $string . '\\Controller') && is_subclass_of('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Module\\Template\\Value\\' . $string . '\\Controller', 'osCommerce\\OM\\Core\\Template\\ValueAbstract') ) {
          call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Module\\Template\\Value\\' . $string . '\\Controller', 'initialize'));
        } elseif ( class_exists('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Value\\' . $string . '\\Controller') && is_subclass_of('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Value\\' . $string . '\\Controller', 'osCommerce\\OM\\Core\\Template\\ValueAbstract') ) {
          call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Value\\' . $string . '\\Controller', 'initialize'));
        }
      }

      if ( strpos($string, ' ') === false ) {
        return $OSCOM_Template->getValue($string);
      } else {
        list($array, $key) = explode(' ', $string, 2);

        $data = $OSCOM_Template->getValue($array);

        return $data[$key];
      }
    }
  }
?>
