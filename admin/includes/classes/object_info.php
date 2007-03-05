<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_ObjectInfo {
    function osC_ObjectInfo($array) {
      foreach ($array as $key => $value) {
        $this->$key = $value;
      }
    }

    function get($value) {
      return $this->$value;
    }

    function set($key, $value) {
      $this->$key = $value;
    }
  }
?>
