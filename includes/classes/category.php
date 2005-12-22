<?php
/*
  $Id: account.php 207 2005-09-26 01:29:31 +0200 (Mo, 26 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Category {
    var $_data = array();

    function osC_Category($id) {
      global $osC_CategoryTree;

      if ($osC_CategoryTree->exists($id)) {
        $this->_data = $osC_CategoryTree->getData($id);
      }
    }

    function getID() {
      return $this->_data['id'];
    }

    function getTitle() {
      return $this->_data['name'];
    }

    function getImage() {
      return $this->_data['image'];
    }

    function hasParent() {
      if ($this->_data['parent_id'] > 0) {
        return true;
      }

      return false;
    }

    function getParent() {
      return $this->_data['parent_id'];
    }

    function getPath() {
      global $osC_CategoryTree;

      return $osC_CategoryTree->buildBreadcrumb($this->_data['id']);
    }
  }
?>
