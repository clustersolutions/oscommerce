<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;

  class OrderTotal extends \osCommerce\OM\Core\Site\Shop\OrderTotal {
    var $_group = 'order_total';

    public function hasKeys() {
      return (count($this->getKeys()) > 0);
    }

    public function install() {
      $OSCOM_Language = Registry::get('Language');

      $data = array('title' => $this->_title,
                    'code' => $this->_code,
                    'author_name' => $this->_author_name,
                    'author_www' => $this->_author_www,
                    'group' => 'OrderTotal');

      OSCOM::callDB('Admin\InsertModule', $data, 'Site');

      foreach ( $OSCOM_Language->getAll() as $key => $value ) {
        if ( file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $key . '/modules/order_total/' . $this->_code . '.xml') ) {
          foreach ( $OSCOM_Language->extractDefinitions($key . '/modules/order_total/' . $this->_code . '.xml') as $def ) {
            $def['id'] = $value['id'];

            OSCOM::callDB('Admin\InsertLanguageDefinition', $def, 'Site');
          }
        }
      }

      Cache::clear('languages');
    }

    public function remove() {
      $OSCOM_Language = Registry::get('Language');

      $data = array('code' => $this->_code,
                    'group' => 'OrderTotal');

      OSCOM::callDB('Admin\DeleteModule', $data, 'Site');

      if ( $this->hasKeys() ) {
        OSCOM::callDB('Admin\DeleteConfigurationParameters', $this->getKeys(), 'Site');

        Cache::clear('configuration');
      }

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $OSCOM_Language->getCode() . '/modules/order_total/' . $this->_code . '.xml') ) {
        foreach ( $OSCOM_Language->extractDefinitions($OSCOM_Language->getCode() . '/modules/order_total/' . $this->_code . '.xml') as $def ) {
          OSCOM::callDB('Admin\DeleteLanguageDefinitions', $def, 'Site');
        }

        Cache::clear('languages');
      }
    }
  }
?>
