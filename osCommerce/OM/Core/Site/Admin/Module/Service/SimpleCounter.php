<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Module\Service;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class SimpleCounter {
    var $title,
        $description,
        $uninstallable = true,
        $depends,
        $precedes;

    public function __construct() {
      $OSCOM_Language = Registry::get('Language');

      $OSCOM_Language->loadIniFile('modules/services/simple_counter.php');

      $this->title = OSCOM::getDef('services_simple_counter_title');
      $this->description = OSCOM::getDef('services_simple_counter_description');
    }

    public function install() {
      return false;
    }

    public function remove() {
      return false;
    }

    public function keys() {
      return false;
    }
  }
?>
