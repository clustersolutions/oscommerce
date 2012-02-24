<?php
/**
 * osCommerce Website
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Module\Template\Widget\message_stack;

  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Template\WidgetAbstract {
    static public function execute() {
      $OSCOM_MessageStack = Registry::get('MessageStack');

      if ( $OSCOM_MessageStack->exists() ) {
        return $OSCOM_MessageStack->get();
      }
    }
  }
?>
