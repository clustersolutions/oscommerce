<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class objectInfo {
    function objectInfo($object_array) {
      foreach ($object_array as $key => $value) {
        $this->$key = $value;
      }
    }
  }
?>
