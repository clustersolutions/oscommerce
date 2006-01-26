<?php
/*
  $Id:language.php 293 2005-11-29 17:34:26Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_language {
    var $title = 'Language',
        $description = 'Include the default or selected language files.',
        $uninstallable = false,
        $depends = 'session',
        $preceeds;

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

    function install() {
      return false;
    }

    function remove() {
      return false;
    }

    function keys() {
      return false;
    }
  }
?>
