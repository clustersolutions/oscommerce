<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core;

  class Language {

/* Private variables */
    var $_code,
        $_languages = array(),
        $_definitions = array();

/* Class constructor */

    function __construct() {
      foreach ( OSCOM::callDB('GetLanguages', null, 'Core') as $lang ) {
        $this->_languages[$lang['code']] = array('id' => (int)$lang['languages_id'],
                                                 'code' => $lang['code'],
                                                 'name' => $lang['name'],
                                                 'locale' => $lang['locale'],
                                                 'charset' => $lang['charset'],
                                                 'date_format_short' => $lang['date_format_short'],
                                                 'date_format_long' => $lang['date_format_long'],
                                                 'time_format' => $lang['time_format'],
                                                 'text_direction' => $lang['text_direction'],
                                                 'currencies_id' => (int)$lang['currencies_id'],
                                                 'numeric_separator_decimal' => $lang['numeric_separator_decimal'],
                                                 'numeric_separator_thousands' => $lang['numeric_separator_thousands'],
                                                 'parent_id' => (int)$lang['parent_id']);
      }

      $this->set();
    }

/* Public methods */

    function load($key, $language_code = null) {
      if ( is_null($language_code) ) {
        $language_code = $this->_code;
      }

      if ( $this->_languages[$language_code]['parent_id'] > 0 ) {
        $this->load($key, $this->getCodeFromID($this->_languages[$language_code]['parent_id']));
      }

      $Qdef = Registry::get('Database')->query('select * from :table_languages_definitions where languages_id = :languages_id and content_group = :content_group');
      $Qdef->bindInt(':languages_id', $this->getData('id', $language_code));
      $Qdef->bindValue(':content_group', $key);
      $Qdef->setCache('languages-' . $language_code . '-' . $key);
      $Qdef->execute();

      while ($Qdef->next()) {
        $this->_definitions[$Qdef->value('definition_key')] = $Qdef->value('definition_value');
      }

      $Qdef->freeResult();
    }

    function get($key) {
      if (isset($this->_definitions[$key])) {
        return $this->_definitions[$key];
      }

      return $key;
    }

    function set($code = null) {
      $this->_code = $code;

      if ( empty($this->_code) ) {
        if ( isset($_GET['language']) ) {
          $this->_code = $_GET['language'];
        } elseif ( isset($_SESSION['language']) ) {
          $this->_code = $_SESSION['language'];
        } elseif ( isset($_COOKIE['language']) ) {
          $this->_code = $_COOKIE['language'];
        } else {
          $this->_code = $this->getBrowserSetting();
        }
      }

      if ( empty($this->_code) || ($this->exists($this->_code) === false) ) {
        $this->_code = DEFAULT_LANGUAGE;
      }

      if ( !isset($_COOKIE['language']) || ($_COOKIE['language'] != $this->_code) ) {
        osc_setcookie('language', $this->_code, time()+60*60*24*90);
      }

      if ( !isset($_SESSION['language']) || ($_SESSION['language'] != $this->_code) ) {
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
            if (preg_match('/^(' . $value . ')(;q=[0-9]\\.[0-9])?$/i', $browser_language) && $this->exists($key)) {
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

    function getData($key, $language = '') {
      if (empty($language)) {
        $language = $this->_code;
      }

      return $this->_languages[$language][$key];
    }

    function getCodeFromID($id) {
      foreach ($this->_languages as $code => $lang) {
        if ($lang['id'] == $id) {
          return $code;
        }
      }
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

    function getLocale() {
      return $this->_languages[$this->_code]['locale'];
    }

    function getCharacterSet() {
      return $this->_languages[$this->_code]['charset'];
    }

    function getDateFormatShort($with_time = false) {
      if ($with_time === true) {
        return $this->_languages[$this->_code]['date_format_short'] . ' ' . $this->getTimeFormat();
      }

      return $this->_languages[$this->_code]['date_format_short'];
    }

    function getDateFormatLong() {
      return $this->_languages[$this->_code]['date_format_long'];
    }

    function getTimeFormat() {
      return $this->_languages[$this->_code]['time_format'];
    }

    function getTextDirection() {
      return $this->_languages[$this->_code]['text_direction'];
    }

    function getCurrencyID() {
      return $this->_languages[$this->_code]['currencies_id'];
    }

    function getNumericDecimalSeparator() {
      return $this->_languages[$this->_code]['numeric_separator_decimal'];
    }

    function getNumericThousandsSeparator() {
      return $this->_languages[$this->_code]['numeric_separator_thousands'];
    }

    function showImage($code = null, $width = '16', $height = '10', $parameters = null) {
      if ( empty($code) ) {
        $code = $this->_code;
      }

      $image_code = strtolower(substr($code, 3));

      if ( !is_numeric($width) ) {
        $width = 16;
      }

      if ( !is_numeric($height) ) {
        $height = 10;
      }

      return osc_image('images/worldflags/' . $image_code . '.png', $this->_languages[$code]['name'], $width, $height, $parameters);
    }
  }
?>
