<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Template_table_based {
    var $_id,
        $_title = 'osCommerce Table Based Template',
        $_code = 'table_based',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_markup_version = 'XHTML 1.0 Transitional',
        $_css_based = '0', /* 0=No; 1=Yes */
        $_medium = 'Screen',
        $_groups = array('boxes' => array('left', 'right'),
                         'content' => array('before', 'after')),
        $_keys;

    function getID() {
      global $osC_Database;

      if (isset($this->_id) === false) {
        $Qtemplate = $osC_Database->query('select id from :table_templates where code = :code');
        $Qtemplate->bindTable(':table_templates', TABLE_TEMPLATES);
        $Qtemplate->bindvalue(':code', $this->_code);
        $Qtemplate->execute();

        $this->_id = $Qtemplate->valueInt('id');
      }

      return $this->_id;
    }

    function getTitle() {
      return $this->_title;
    }

    function getCode() {
      return $this->_code;
    }

    function getAuthorName() {
      return $this->_author_name;
    }

    function getAuthorAddress() {
      return $this->_author_www;
    }

    function getMarkup() {
      return $this->_markup_version;
    }

    function isCSSBased() {
      return ($this->_css_based == '1');
    }

    function getMedium() {
      return $this->_medium;
    }

    function getGroups($group) {
      return $this->_groups[$group];
    }

    function install() {
      global $osC_Database;

      $Qinstall = $osC_Database->query('insert into :table_templates (title, code, author_name, author_www, markup_version, css_based, medium) values (:title, :code, :author_name, :author_www, :markup_version, :css_based, :medium)');
      $Qinstall->bindTable(':table_templates', TABLE_TEMPLATES);
      $Qinstall->bindValue(':title', $this->_title);
      $Qinstall->bindValue(':code', $this->_code);
      $Qinstall->bindValue(':author_name', $this->_author_name);
      $Qinstall->bindValue(':author_www', $this->_author_www);
      $Qinstall->bindValue(':markup_version', $this->_markup_version);
      $Qinstall->bindValue(':css_based', $this->_css_based);
      $Qinstall->bindValue(':medium', $this->_medium);
      $Qinstall->execute();
    }

    function remove() {
      global $osC_Database;

      $Qdel = $osC_Database->query('delete from :table_templates_boxes_to_pages where templates_id = :templates_id');
      $Qdel->bindTable(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
      $Qdel->bindValue(':templates_id', $this->getID());
      $Qdel->execute();

      $Qdel = $osC_Database->query('delete from :table_templates where id = :id');
      $Qdel->bindTable(':table_templates', TABLE_TEMPLATES);
      $Qdel->bindValue(':id', $this->getID());
      $Qdel->execute();

      if ($this->hasKeys()) {
        $Qdel = $osC_Database->query('delete from :table_configuration where configuration_key in (":configuration_key")');
        $Qdel->bindTable(':table_configuration', TABLE_CONFIGURATION);
        $Qdel->bindRaw(':configuration_key', implode('", "', $this->getKeys()));
        $Qdel->execute();
      }
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array();
      }

      return $this->_keys;
    }

    function hasKeys() {
      static $has_keys;

      if (isset($has_keys) === false) {
        $has_keys = (sizeof($this->getKeys()) > 0) ? true : false;
      }

      return $has_keys;
    }

    function isInstalled() {
      global $osC_Database;

      static $is_installed;

      if (isset($is_installed) === false) {
        $Qcheck = $osC_Database->query('select id from :table_templates where code = :code');
        $Qcheck->bindTable(':table_templates', TABLE_TEMPLATES);
        $Qcheck->bindValue(':code', $this->_code);
        $Qcheck->execute();

        $is_installed = ($Qcheck->numberOfRows()) ? true : false;
      }

      return $is_installed;
    }

    function isActive() {
      return true;
    }
  }
?>
