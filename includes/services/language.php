<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

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

      require('includes/languages/' . $_SESSION['language'] . '.php');

      header('Content-Type: text/html; charset=' . CHARSET);

      setlocale(LC_TIME, LANGUAGE_LOCALE);

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
