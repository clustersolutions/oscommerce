<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Language {

/* Private variables */
    var $_code,
        $_languages = array();

/* Class constructor */

    function osC_Language() {
      global $osC_Database;

      $Qlanguages = $osC_Database->query('select * from :table_languages order by sort_order, name');
      $Qlanguages->bindTable(':table_languages', TABLE_LANGUAGES);
      $Qlanguages->setCache('languages');
      $Qlanguages->execute();

      while ($Qlanguages->next()) {
        $this->_languages[$Qlanguages->value('code')] = array('id' => $Qlanguages->valueInt('languages_id'),
                                                              'name' => $Qlanguages->value('name'),
                                                              'image' => $Qlanguages->value('image'),
                                                              'directory' => $Qlanguages->value('directory'));
      }

      $Qlanguages->freeResult();

      $this->set();
    }

/* Public methods */

    function set($code = '') {
      $this->_code = $code;

      if (empty($this->_code)) {
        if (isset($_SESSION['language'])) {
          $this->_code = $_SESSION['language'];
        } elseif (isset($_COOKIE['language'])) {
          $this->_code = $_COOKIE['language'];
        } else {
          $this->_code = $this->getBrowserSetting();
        }
      }

      if (empty($this->_code) || ($this->exists($this->_code) === false)) {
        $this->_code = DEFAULT_LANGUAGE;
      }

      if (!isset($_COOKIE['language']) || (isset($_COOKIE['language']) && ($_COOKIE['language'] != $this->_code))) {
        tep_setcookie('language', $this->_code, time()+60*60*24*90);
      }

      if ((isset($_SESSION['language']) === false) || (isset($_SESSION['language']) && ($_SESSION['language'] != $this->_code))) {
        $_SESSION['language'] = $this->_code;
      }
    }

    function getBrowserSetting() {
      if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

        $languages = array('ar' => 'ar([-_][[:alpha:]]{2})?|arabic',
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

        foreach ($browser_languages as $browser_language) {
          foreach ($languages as $key => $value) {
            if (eregi('^(' . $value . ')(;q=[0-9]\\.[0-9])?$', $browser_language) && $this->exists($key)) {
              return $key;
            }
          }
        }
      }

      return false;
    }

    function exists($code) {
      return array_key_exists($code, $this->_languages);
    }

    function getAll() {
      return $this->_languages;
    }

    function getID() {
      return $this->_languages[$this->_code]['id'];
    }

    function getName() {
      return $this->_languages[$this->_code]['name'];
    }

    function getCode() {
      return $this->_code;
    }

    function getImage() {
      return $this->_languages[$this->_code]['image'];
    }

    function getDirectory() {
      return $this->_languages[$this->_code]['directory'];
    }

    function load($definition = false) {
      if (is_string($definition)) {
        include('includes/languages/' . $this->getDirectory() . '/' . $definition);
      } else {
        include('includes/languages/' . $this->getDirectory() . '.php');
      }
    }
  }
?>
