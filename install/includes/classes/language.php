<?php
/*
  $Id: language.php 199 2005-09-22 17:56:13 +0200 (Do, 22 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('../admin/includes/classes/language.php');

  class osC_LanguageInstall extends osC_Language_Admin {

/* Private variables */
    var $_languages = array('en_US' => array('code' => 'en_US', 'directory' => 'english'), 'es_ES' => array('code' => 'es_ES', 'directory' => 'espanol'), 'de_DE' => array('code' => 'de_DE', 'directory' => 'german'));

/* Class constructor */

    function osC_LanguageInstall() {
      $language = (isset($_GET['language']) && !empty($_GET['language']) ? $_GET['language'] : '');

      $this->set($language);

      $this->_definitions = $this->_parseIniFile();

      $this->load(basename($_SERVER['SCRIPT_FILENAME']));
    }

/* Public methods */

    function load($filename) {
      $this->_definitions = array_merge($this->_definitions, $this->_parseIniFile($filename));
    }

    function getDirectory() {
      return $this->_languages[$this->_code]['directory'];
    }

/* Private methods */

    function _parseIniFile($filename = '', $comment = '#') {
      if (empty($filename)) {
        $contents = file('includes/languages/' . $this->getDirectory() . '.php');
      } else {
        if (file_exists('includes/languages/' . $this->getDirectory() . '/' . $filename) === false) {
          return array();
        }

        $contents = file('includes/languages/' . $this->getDirectory() . '/' . $filename);
      }

      $ini_array = array();

      foreach ($contents as $line) {
        $line = trim($line);

        $firstchar = substr($line, 0, 1);

        if (!empty($line) && ($firstchar != $comment)) {
          $delimiter = strpos($line, '=');

          if ($delimiter !== false) {
            $key = trim(substr($line, 0, $delimiter));
            $value = trim(substr($line, $delimiter + 1));

            $ini_array[$key] = $value;
          } elseif (isset($key)) {
            $ini_array[$key] .= trim($line);
          }
        }
      }

      unset($contents);

      return $ini_array;
    }
  }
?>
