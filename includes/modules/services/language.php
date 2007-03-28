<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_language {
    function start() {
      global $osC_Language, $osC_Session;

      require('includes/classes/language.php');
      $osC_Language = new osC_Language();

      if (isset($_GET['language']) && !empty($_GET['language'])) {
        $osC_Language->set($_GET['language']);
      }

      $osC_Language->load('general');
      $osC_Language->load('modules-boxes');
      $osC_Language->load('modules-content');

      header('Content-Type: text/html; charset=' . $osC_Language->getCharacterSet());

      osc_setlocale(LC_TIME, explode(',', $osC_Language->getLocale()));

      return true;
    }

    function stop() {
      return true;
    }
  }
?>
