<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Language {

/* Public variables */

    var $language;

/* Private variables */
    var $_languages = array();

/* Class constructor */

    function osC_Language() {
      global $osC_Database, $osC_Session;

      $Qlanguages = $osC_Database->query('select * from :table_languages order by sort_order, name');
      $Qlanguages->bindTable(':table_languages', TABLE_LANGUAGES);
      $Qlanguages->setCache('languages');
      $Qlanguages->execute();

      while ($Qlanguages->next()) {
        $this->_languages[$Qlanguages->value('code')] = array('id' => $Qlanguages->valueInt('languages_id'),
                                                              'name' => $Qlanguages->value('name'),
                                                              'code' => $Qlanguages->value('code'),
                                                              'image' => $Qlanguages->value('image'),
                                                              'directory' => $Qlanguages->value('directory'));
      }

      $Qlanguages->freeResult();

      if ($osC_Session->exists('language')) {
        $this->set();
      } else {
        $this->setToBrowser();
      }
    }

/* Public methods */

    function set($lang = '') {
      if (PHP_VERSION < 4.1) {
        global $_COOKIE;
      }

      global $osC_Session;

      if (empty($lang) && $osC_Session->exists('language')) {
        foreach ($this->_languages as $l) {
          if ($l['directory'] == $osC_Session->value('language')) {
            $lang = $l['code'];
            break;
          }
        }
      }

      if (empty($lang) || ($this->exists($lang) === false)) {
        $lang = DEFAULT_LANGUAGE;
      }

      $this->language = $this->get($lang);

      if (!isset($_COOKIE['language']) || (isset($_COOKIE['language']) && ($_COOKIE['language'] != $this->language['code']))) {
        tep_setcookie('language', $this->language['code'], time()+60*60*24*90);
      }

      if (($osC_Session->exists('language') === false) || ($osC_Session->exists('language') && ($osC_Session->value('language') != $this->language['directory']))) {
        $osC_Session->set('language', $this->language['directory']);
        $osC_Session->set('languages_id', $this->language['id']);
      }
    }

    function setToBrowser() {
      if (PHP_VERSION < 4.1) {
        global $_COOKIE, $_SERVER;
      }

      if (isset($_COOKIE['language'])) {
        if ($this->exists($_COOKIE['language'])) {
          $this->set($_COOKIE['language']);

          return true;
        }
      }

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
            $this->set($key);

            return true;
          }
        }
      }

      $this->set(DEFAULT_LANGUAGE);
    }

    function get($lang) {
      return $this->_languages[$lang];
    }

    function getAll() {
      return $this->_languages;
    }

    function exists($lang) {
      return array_key_exists($lang, $this->_languages);
    }

    function getID() {
      return $this->language['id'];
    }

    function getName() {
      return $this->language['name'];
    }

    function getCode() {
      return $this->language['code'];
    }

    function getImage() {
      return $this->language['image'];
    }

    function getDirectory() {
      return $this->language['directory'];
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
