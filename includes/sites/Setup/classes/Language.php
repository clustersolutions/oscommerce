<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Setup_Language extends OSCOM_Site_Admin_Language {
    public function __construct() {
      $OSCOM_DirectoryListing = new OSCOM_DirectoryListing(OSCOM::BASE_DIRECTORY . 'languages');
      $OSCOM_DirectoryListing->setIncludeDirectories(false);
      $OSCOM_DirectoryListing->setCheckExtension('xml');

      foreach ( $OSCOM_DirectoryListing->getFiles() as $file ) {
        $lang = OSCOM_XML::toArray(simplexml_load_file(OSCOM::BASE_DIRECTORY . 'languages/' . $file['name']));

        if ( !isset($lang['language']) ) { // create root element (simpleXML does not use root element)
          $lang = array('language' => $lang);
        }

        $this->_languages[$lang['language']['data']['code']] = array('id' => 1, //HPDL to remove
                                                                     'code' => $lang['language']['data']['code'],
                                                                     'name' => $lang['language']['data']['title'],
                                                                     'locale' => $lang['language']['data']['locale'],
                                                                     'charset' => $lang['language']['data']['character_set'],
                                                                     'date_format_short' => $lang['language']['data']['date_format_short'],
                                                                     'date_format_long' => $lang['language']['data']['date_format_long'],
                                                                     'time_format' => $lang['language']['data']['time_format'],
                                                                     'text_direction' => $lang['language']['data']['text_direction'],
                                                                     'parent_id' => 0);
      }

      unset($lang);

      $language = (isset($_GET['language']) && !empty($_GET['language']) ? $_GET['language'] : '');

      $this->set($language);

      header('Content-Type: text/html; charset=' . $this->getCharacterSet());
      setlocale(LC_TIME, explode(',', $this->getLocale()));

      $this->loadIniFile();
      $this->loadIniFile(OSCOM::getSiteApplication() . '.php');
    }

    public function loadIniFile($filename = null, $comment = '#', $language_code = null) {
      if ( is_null($language_code) ) {
        $language_code = $this->_code;
      }

      if ( $this->_languages[$language_code]['parent_id'] > 0 ) {
        $this->loadIniFile($filename, $comment, $this->getCodeFromID($this->_languages[$language_code]['parent_id']));
      }

      if ( is_null($filename) ) {
        if ( file_exists(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/languages/' . $language_code . '.php') ) {
          $contents = file(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/languages/' . $language_code . '.php');
        } else {
          return array();
        }
      } else {
        if ( substr(realpath(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/languages/' . $language_code . '/' . $filename), 0, strlen(realpath(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/languages/' . $language_code))) != realpath(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/languages/' . $language_code) ) {
          return array();
        }

        if ( !file_exists(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/languages/' . $language_code . '/' . $filename) ) {
          return array();
        }

        $contents = file(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/languages/' . $language_code . '/' . $filename);
      }

      $ini_array = array();

      foreach ( $contents as $line ) {
        $line = trim($line);

        $firstchar = substr($line, 0, 1);

        if ( !empty($line) && ( $firstchar != $comment) ) {
          $delimiter = strpos($line, '=');

          if ( $delimiter !== false ) {
            $key = trim(substr($line, 0, $delimiter));
            $value = trim(substr($line, $delimiter + 1));

            $ini_array[$key] = $value;
          } elseif ( isset($key) ) {
            $ini_array[$key] .= trim($line);
          }
        }
      }

      unset($contents);

      $this->_definitions = array_merge($this->_definitions, $ini_array);
    }
  }
?>
