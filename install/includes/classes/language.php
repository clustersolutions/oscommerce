<?php
/*
  $Id: language.php 199 2005-09-22 17:56:13 +0200 (Do, 22 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Language {

/* Public variables */

    var $language;

/* Private variables */
    var $_languages = array('en' => 'english', 'es' => 'espanol', 'de' => 'german'),
        $_values = array();

/* Class constructor */

    function osC_Language() {
      if (isset($_GET['language'])) {
        $language = $_GET['language'];
      } elseif (isset($_COOKIE['language'])) {
        $language = $_COOKIE['language'];
      } else {
        $language = $this->getBrowserLanguage();
      }

      if ($this->exists($language) === false) {
        $language = 'en';
      }

      $this->set($language);

      $this->_values = $this->_parseIniFile();

      $this->load(basename($_SERVER['PHP_SELF']));
    }

/* Public methods */

    function load($filename) {
      $this->_values = array_merge($this->_values, $this->_parseIniFile($filename));
    }

    function get($key) {
      return $this->_values[$key];
    }

/* Older functions */

    function set($language) {
      if ( (isset($_COOKIE['language']) === false) || (isset($_COOKIE['language']) && ($_COOKIE['language'] != $language)) ) {
        setcookie('language', $language);
      }

      $this->language = $language;
    }

    function getBrowserLanguage() {
      $language = 'en';

      $http_accept_language = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

      $browser_languages = array('ar' => 'ar([-_][[:alpha:]]{2})?|arabic',
                                 'bg' => 'bg|bulgarian',
                                 'br' => 'pt[-_]br|brazilian portuguese',
                                 'ca' => 'ca|catalan',
                                 'cs' => 'cs|czech',
                                 'da' => 'da|danish',
                                 'de' => 'de([-_][[:alpha:]]{2})?|german',
                                 'el' => 'el|greek',
                                 'en' => 'en([-_][[:alpha:]]{2})?|english',
                                 'es' => 'es([-_][[:alpha:]]{2})?|spanish',
                                 'et' => 'et|estonian',
                                 'fi' => 'fi|finnish',
                                 'fr' => 'fr([-_][[:alpha:]]{2})?|french',
                                 'gl' => 'gl|galician',
                                 'he' => 'he|hebrew',
                                 'hu' => 'hu|hungarian',
                                 'id' => 'id|indonesian',
                                 'it' => 'it|italian',
                                 'ja' => 'ja|japanese',
                                 'ko' => 'ko|korean',
                                 'ka' => 'ka|georgian',
                                 'lt' => 'lt|lithuanian',
                                 'lv' => 'lv|latvian',
                                 'nl' => 'nl([-_][[:alpha:]]{2})?|dutch',
                                 'no' => 'no|norwegian',
                                 'pl' => 'pl|polish',
                                 'pt' => 'pt([-_][[:alpha:]]{2})?|portuguese',
                                 'ro' => 'ro|romanian',
                                 'ru' => 'ru|russian',
                                 'sk' => 'sk|slovak',
                                 'sr' => 'sr|serbian',
                                 'sv' => 'sv|swedish',
                                 'th' => 'th|thai',
                                 'tr' => 'tr|turkish',
                                 'uk' => 'uk|ukrainian',
                                 'tw' => 'zh[-_]tw|chinese traditional',
                                 'zh' => 'zh|chinese simplified');

      foreach ($http_accept_language as $browser_language) {
        foreach ($browser_languages as $key => $value) {
          if (eregi('^(' . $value . ')(;q=[0-9]\\.[0-9])?$', $browser_language) && $this->exists($key)) {
            $language = $key;

            break 2;
          }
        }
      }

      if (isset($this->_languages[$language])) {
        return $language;
      } else {
        return 'en';
      }
    }

    function exists($language) {
      return is_dir('includes/languages/' . $this->_languages[$language]);
    }

/* Private methods */

    function _parseIniFile($filename = '', $comment = '#') {
      if (empty($filename)) {
        $contents = file('includes/languages/' . $this->_languages[$this->language] . '.php');
      } else {
        if (file_exists('includes/languages/' . $this->_languages[$this->language] . '/' . $filename) === false) {
          return array();
        }

        $contents = file('includes/languages/' . $this->_languages[$this->language] . '/' . $filename);
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
