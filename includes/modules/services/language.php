<?php
/*
  $Id: language.php,v 1.4 2004/11/24 15:33:38 hpdl Exp $

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
      if (PHP_VERSION < 4.1) {
        global $_GET;
      }

      global $osC_Session, $osC_Language;

      require('includes/classes/language.php');
      $osC_Language = new osC_Language();

      if (isset($_GET['language']) && !empty($_GET['language'])) {
        $osC_Language->set($_GET['language']);
      }

      require('includes/languages/' . $osC_Session->value('language') . '.php');

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
