<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Login;

  use osCommerce\OM\Core\OSCOM;

  class Controller extends \osCommerce\OM\Core\Site\Admin\ApplicationAbstract {
    protected $_link_to = false;
    protected $_icon = 'login.png';
    protected $_page_contents = 'main.html'; // HPDL (html should be the default)

    protected function initialize() {}

    protected function process() {
      $this->_page_title = OSCOM::getDef('heading_title');
    }
  }
?>
