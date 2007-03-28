<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_sefu_Admin {
    var $title,
        $description,
        $uninstallable = true,
        $depends,
        $precedes = 'session';

    function osC_Services_sefu_Admin() {
      global $osC_Language;

      $osC_Language->loadIniFile('modules/services/sefu.php');

      $this->title = $osC_Language->get('services_sefu_title');
      $this->description = $osC_Language->get('services_sefu_description');
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
