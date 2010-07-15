<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Admin;

  use osCommerce\OM\OSCOM;

  abstract class ApplicationAbstract extends \osCommerce\OM\ApplicationAbstract {
    protected $_link_to = true;
    protected $_group;
    protected $_icon = 'default.png';
    protected $_title;
    protected $_sort_order;

    public function __construct($process = true) {
      $this->initialize();

      if ( $process === true ) {
        $this->process();

        if ( isset($_GET['action']) && !empty($_GET['action']) ) {
          $action = osc_sanitize_string(basename($_GET['action']));

          if ( class_exists('osCommerce\\OM\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . $action) ) {
            call_user_func(array('osCommerce\\OM\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . $action, 'execute'), $this);
          }
        }
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
