<?php
/*
  $Id: object_info.php,v 1.7 2004/07/22 22:17:08 hpdl Exp $

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
