<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  abstract class OSCOM_Site_Admin_ApplicationAbstract extends OSCOM_ApplicationAbstract {
    protected $_link_to = true;
    protected $_group;
    protected $_subgroups = array();
    protected $_icon;
    protected $_title;
    protected $_sort_order;

    public function __construct($process = true) {
      $this->initialize();

      if ( $process === true ) {
        $this->process();

        if ( isset($_GET['action']) && !empty($_GET['action']) ) {
          $action = osc_sanitize_string(basename($_GET['action']));

          if ( class_exists('OSCOM_Site_' . OSCOM::getSite() . '_Application_' . OSCOM::getSiteApplication() . '_Action_' . $action) ) {
            call_user_func(array('OSCOM_Site_' . OSCOM::getSite() . '_Application_' . OSCOM::getSiteApplication() . '_Action_' . $action, 'execute'), $this);
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

    public function getSubGroups() {
      return $this->_subgroups;
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
