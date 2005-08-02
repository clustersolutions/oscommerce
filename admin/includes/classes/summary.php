<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Summary {

/* Private methods */

    var $_title,
        $_title_link,
        $_data;

/* Public methods */

    function getTitle() {
      return $this->_title;
    }

    function getTitleLink() {
      return $this->_title_link;
    }

    function hasTitleLink() {
      if (isset($this->_title_link) && !empty($this->_title_link)) {
        return true;
      }

      return false;
    }

    function getData() {
      return $this->_data;
    }

    function hasData() {
      if (isset($this->_data) && !empty($this->_data)) {
        return true;
      }

      return false;
    }
  }
?>
