<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  abstract class ApplicationAbstract extends \osCommerce\OM\Core\ApplicationAbstract {
    protected $_link_to = true;
    protected $_group;
    protected $_icon = 'default.png';
    protected $_title;
    protected $_sort_order;

    public function __construct($process = true) {
      $this->ignoreAction(Registry::get('Session')->getName());

      $this->initialize();

      if ( $process === true ) {
        $this->process();

        $this->runActions();
      }
    }

    public function canLinkTo() {
      return $this->_link_to;
    }

    public function getGroup() {
      return $this->_group;
    }

    public function getIcon() {
      return $this->_icon;
    }

    public function getTitle() {
      return $this->_title;
    }

    public function getSortOrder() {
      return $this->_sort_order;
    }
  }
?>
