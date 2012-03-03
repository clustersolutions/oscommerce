<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Module\Template\Value\current_site_application;

  use osCommerce\OM\Core\OSCOM;

  class Controller extends \osCommerce\OM\Core\Template\ValueAbstract {
    static public function execute() {
      return OSCOM::getSiteApplication();
    }
  }
?>
