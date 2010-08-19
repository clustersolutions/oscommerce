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

  abstract class PaymentModuleAbstract {
    protected $_code;
    protected $_title;
    protected $_description;
    protected $_author_name;
    protected $_author_www;
    protected $_status;
    protected $_sort_order = 0;

    abstract protected function initialize();
    abstract public function isInstalled();

    public function __construct() {
      $module_class = explode('\\', get_called_class());
      $this->_code = end($module_class);

      $this->initialize();
    }

    public function isEnabled() {
      return $this->_status;
    }

    public function getCode() {
      return $this->_code;
    }

    public function getTitle() {
      return $this->_title;
    }

    public function getSortOrder() {
      return $this->_sort_order;
    }

    public function hasKeys() {
      return (count($this->getKeys()) > 0);
    }

    public function getKeys() {
      return array();
    }

    public function install() {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      $Qinstall = $OSCOM_Database->query('insert into :table_templates_boxes (title, code, author_name, author_www, modules_group) values (:title, :code, :author_name, :author_www, :modules_group)');
      $Qinstall->bindValue(':title', $this->_title);
      $Qinstall->bindValue(':code', $this->_code);
      $Qinstall->bindValue(':author_name', $this->_author_name);
      $Qinstall->bindValue(':author_www', $this->_author_www);
      $Qinstall->bindValue(':modules_group', 'payment');
      $Qinstall->execute();

      foreach ( $OSCOM_Language->getAll() as $key => $value ) {
        if ( file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $key . '/modules/payment/' . $this->_code . '.xml') ) {
          foreach ( $OSCOM_Language->extractDefinitions($key . '/modules/payment/' . $this->_code . '.xml') as $def ) {
            $Qcheck = $OSCOM_Database->query('select id from :table_languages_definitions where definition_key = :definition_key and content_group = :content_group and languages_id = :languages_id limit 1');
            $Qcheck->bindValue(':definition_key', $def['key']);
            $Qcheck->bindValue(':content_group', $def['group']);
            $Qcheck->bindInt(':languages_id', $value['id']);
            $Qcheck->execute();

            if ( $Qcheck->numberOfRows() === 1 ) {
              $Qdef = $OSCOM_Database->query('update :table_languages_definitions set definition_value = :definition_value where definition_key = :definition_key and content_group = :content_group and languages_id = :languages_id');
            } else {
              $Qdef = $OSCOM_Database->query('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
            }
            $Qdef->bindInt(':languages_id', $value['id']);
            $Qdef->bindValue(':content_group', $def['group']);
            $Qdef->bindValue(':definition_key', $def['key']);
            $Qdef->bindValue(':definition_value', $def['value']);
            $Qdef->execute();
          }
        }
      }

      Cache::clear('languages');
    }

    public function remove() {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      $Qdel = $OSCOM_Database->query('delete from :table_templates_boxes where code = :code and modules_group = :modules_group');
      $Qdel->bindValue(':code', $this->_code);
      $Qdel->bindValue(':modules_group', 'payment');
      $Qdel->execute();

      if ( $this->hasKeys() ) {
        $Qdel = $OSCOM_Database->query('delete from :table_configuration where configuration_key in (":configuration_key")');
        $Qdel->bindRaw(':configuration_key', implode('", "', $this->getKeys()));
        $Qdel->execute();
      }

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $OSCOM_Language->getCode() . '/modules/payment/' . $this->_code . '.xml') ) {
        foreach ( $OSCOM_Language->extractDefinitions($OSCOM_Language->getCode() . '/modules/payment/' . $this->_code . '.xml') as $def ) {
          $Qdel = $OSCOM_Database->query('delete from :table_languages_definitions where definition_key = :definition_key and content_group = :content_group');
          $Qdel->bindValue(':definition_key', $def['key']);
          $Qdel->bindValue(':content_group', $def['group']);
          $Qdel->execute();
        }

        Cache::clear('languages');
      }
    }
  }
?>
