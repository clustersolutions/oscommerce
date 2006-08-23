<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  function osc_realpath($directory) {
    return str_replace('\\', '/', realpath($directory));
  }
?>
