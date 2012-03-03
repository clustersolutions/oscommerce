<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\Module\Template\Value\cfg_input_field;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\Site\Admin\Application\Configuration\Configuration;

  class Controller extends \osCommerce\OM\Core\Template\ValueAbstract {
    static public function execute() {
      $OSCOM_ObjectInfo = new ObjectInfo(Configuration::getEntry($_GET['pID']));

      if ( strlen($OSCOM_ObjectInfo->get('set_function')) > 0 ) {
        $value_field = Configuration::callUserFunc($OSCOM_ObjectInfo->get('set_function'), $OSCOM_ObjectInfo->get('configuration_value'), $OSCOM_ObjectInfo->get('configuration_key'));
      } else {
        $value_field = HTML::inputField('configuration[' . $OSCOM_ObjectInfo->get('configuration_key') . ']', $OSCOM_ObjectInfo->get('configuration_value'));
      }

      return $value_field;
    }
  }
?>
