<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Login;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Site\Admin\ApplicationAbstract {
    protected $_link_to = false;
    protected $_icon = 'login.png';
    protected $_page_contents = 'main.html'; // HPDL (html should be the default)

    protected function initialize() {}

    protected function process() {
      $OSCOM_Template = Registry::get('Template');

      $this->_page_title = OSCOM::getDef('heading_title');

      $OSCOM_Template->setValue('page_icon', $OSCOM_Template->getIcon(32, $this->_icon));
      $OSCOM_Template->setValue('page_title', $this->_page_title);
      $OSCOM_Template->setValue('lang_field_show_password', addslashes(OSCOM::getDef('field_show_password')));
    }
  }
?>
