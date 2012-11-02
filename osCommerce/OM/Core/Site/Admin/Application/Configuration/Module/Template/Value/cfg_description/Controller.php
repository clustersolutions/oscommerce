<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\Module\Template\Value\cfg_description;

  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\Site\Admin\Application\Configuration\Configuration;

  class Controller extends \osCommerce\OM\Core\Template\ValueAbstract {
    static public function execute() {
      $OSCOM_ObjectInfo = new ObjectInfo(Configuration::getEntry($_GET['pID']));

      return $OSCOM_ObjectInfo->get('configuration_description');
    }
  }
?>
