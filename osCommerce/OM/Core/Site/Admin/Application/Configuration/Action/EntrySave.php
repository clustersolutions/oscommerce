<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\Application\Configuration\Configuration;

  class EntrySave {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Template = Registry::get('Template');

      $application->setPageContent('entries_edit.php');

      $OSCOM_ObjectInfo = new ObjectInfo(Configuration::getEntry($_GET['pID']));

      if ( strlen($OSCOM_ObjectInfo->get('set_function')) > 0 ) {
        $value_field = Configuration::callUserFunc($OSCOM_ObjectInfo->get('set_function'), $OSCOM_ObjectInfo->get('configuration_value'), $OSCOM_ObjectInfo->get('configuration_key'));
      } else {
        $value_field = HTML::inputField('configuration[' . $OSCOM_ObjectInfo->get('configuration_key') . ']', $OSCOM_ObjectInfo->get('configuration_value'));
      }

      $OSCOM_Template->setValue('cfg_id', $OSCOM_ObjectInfo->getInt('configuration_id'));
      $OSCOM_Template->setValue('cfg_title', $OSCOM_ObjectInfo->getProtected('configuration_title'));
      $OSCOM_Template->setValue('cfg_description', $OSCOM_ObjectInfo->getProtected('configuration_description'));
      $OSCOM_Template->setValue('cfg_key', $OSCOM_ObjectInfo->getProtected('configuration_key'));
      $OSCOM_Template->setValue('cfg_input_field', $value_field);
    }
  }
?>
