<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_RPC_Language extends OSCOM_Site_Admin_Language {
    protected $_site = 'Admin';

    public function __construct() {
      parent::__construct();
    }

    public function setSite($site) {
      $this->_site = $site;
    }

    public function getSite($site) {
      return $this->_site;
    }

    public function loadIniFile($filename = null, $comment = '#', $language_code = null) {
      if ( is_null($language_code) ) {
        $language_code = $this->_code;
      }

      if ( $this->_languages[$language_code]['parent_id'] > 0 ) {
        $this->loadIniFile($filename, $comment, $this->getCodeFromID($this->_languages[$language_code]['parent_id']));
      }

      if ( is_null($filename) ) {
        if ( file_exists(OSCOM::BASE_DIRECTORY . 'sites/' . $this->_site . '/languages/' . $language_code . '.php') ) {
          $contents = file(OSCOM::BASE_DIRECTORY . 'sites/' . $this->_site . '/languages/' . $language_code . '.php');
        } else {
          return array();
        }
      } else {
        if ( substr(realpath(OSCOM::BASE_DIRECTORY . 'sites/' . $this->_site . '/languages/' . $language_code . '/' . $filename), 0, strlen(realpath(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSiteApplication() . '/languages/' . $language_code))) != realpath(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSiteApplication() . '/languages/' . $language_code) ) {
          return array();
        }

        if ( !file_exists(OSCOM::BASE_DIRECTORY . 'sites/' . $this->_site . '/languages/' . $language_code . '/' . $filename) ) {
          return array();
        }

        $contents = file(OSCOM::BASE_DIRECTORY . 'sites/' . $this->_site . '/languages/' . $language_code . '/' . $filename);
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
