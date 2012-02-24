<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Site\Admin\ApplicationAbstract {
    protected $_group = 'configuration';
    protected $_icon = 'configuration.png';
    protected $_sort_order = 200;
    protected $_page_contents = 'main.html'; // HPDL (html should be the default)

    protected function initialize() {
      $this->_title = OSCOM::getDef('app_title');
    }

    protected function process() {
      $OSCOM_Template = Registry::get('Template');

      $this->_page_title = OSCOM::getDef('heading_title');

      if ( isset($_GET['id']) && is_numeric($_GET['id']) ) {
        $this->_page_contents = 'entries.html';
        $this->_page_title .= ': ' . Configuration::get($_GET['id'], 'configuration_group_title');

        $OSCOM_Template->setValue('group_id', $_GET['id']);
      }

      $OSCOM_Template->setValue('page_icon', $OSCOM_Template->getIcon(32, $this->_icon));
      $OSCOM_Template->setValue('page_title', $this->_page_title);
    }
  }
?>
