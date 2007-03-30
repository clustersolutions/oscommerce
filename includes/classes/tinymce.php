<?php
/*
  $Id: account.php 207 2005-09-26 01:29:31 +0200 (Mo, 26 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_TinyMCE {
    var $_themes_array = array();

    function getThemes() {
      $osC_DirectoryListing = new osC_DirectoryListing('external/tiny_mce/themes');
      $osC_DirectoryListing->setIncludeFiles(false);
      foreach ($osC_DirectoryListing->getFiles() as $file) {
        $this->_themes_array[] = array('themeid' => $file['name'],
                                'themename' => $file['name']);
      }
      return $this->_themes_array;
    }

    function getTheme($id) {
      $osC_DirectoryListing = new osC_DirectoryListing('external/tiny_mce/themes');
      $osC_DirectoryListing->setIncludeFiles(false);
      foreach ($osC_DirectoryListing->getFiles() as $file) {
        if ($file['name']==$id) {
          return $file['name'];
        }
      }
      return false;
    }
  }
?>
