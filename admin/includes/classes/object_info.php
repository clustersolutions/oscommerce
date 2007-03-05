<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_ObjectInfo {
    var $_keys = array();

    function osC_ObjectInfo($array) {
      foreach ($array as $key => $value) {
        $this->_keys[$key] = $value;
      }
    }

    function get($key) {
      return $this->_keys[$key];
    }

    function getAll() {
      return $this->_keys;
    }
      
    function set($key, $value) {
      $this->_keys[$key] = $value;
    }
  }
?>
