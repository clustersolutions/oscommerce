<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  use osCommerce\OM\Core\OSCOM;

  class widget extends \osCommerce\OM\Core\Template\TagAbstract {
    static public function execute($string) {
      $params = explode('|', $string, 2);

      if ( !isset($params[1]) ) {
        $params[1] = null;
      }

      $widget = $params[0];

      if ( class_exists('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Widget\\' . $widget . '\\Controller') && is_subclass_of('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Widget\\' . $widget . '\\Controller', 'osCommerce\\OM\\Core\\Template\\WidgetAbstract') ) {
        return call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Widget\\' . $widget . '\\Controller', 'initialize'), $params[1]);
      } else {
        trigger_error('Template Widget {' . $widget . '} does not exist for ' . OSCOM::getSite());
      }

      return false;
    }
  }
?>
