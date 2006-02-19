<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_OrderTotal {
    var $_modules = array(),
        $_data = array(),
        $_group = 'order_total';

// class constructor
    function osC_OrderTotal() {
      global $osC_Database, $osC_Language;

      $Qmodules = $osC_Database->query('select code from :table_templates_boxes where modules_group = "order_total"');
      $Qmodules->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
      $Qmodules->setCache('modules-order_total');
      $Qmodules->execute();

      while ($Qmodules->next()) {
        $this->_modules[] = $Qmodules->value('code');
      }

      $Qmodules->freeResult();

      $osC_Language->load('modules-order_total');

      foreach ($this->_modules as $module) {
        $module_class = 'osC_OrderTotal_' . $module;

        if (class_exists($module_class) === false) {
          include('includes/modules/order_total/' . $module . '.' . substr(basename(__FILE__), (strrpos(basename(__FILE__), '.')+1)));
        }

        $GLOBALS[$module_class] = new $module_class();
      }

      usort($this->_modules, array('osC_OrderTotal', '_usortModules'));
    }

// class methods
    function getCode() {
      return $this->_code;
    }

    function getTitle() {
      return $this->_title;
    }

    function getDescription() {
      return $this->_description;
    }

    function getStatus() {
      return $this->_status;
    }

    function getSortOrder() {
      return $this->_sort_order;
    }

    function &getResult() {
      global $osC_ShoppingCart;

      $this->_data = array();

      foreach ($this->_modules as $module) {
        $module = 'osC_OrderTotal_' . $module;

        if ($GLOBALS[$module]->getStatus() === true) {
          $GLOBALS[$module]->process();

          foreach ($GLOBALS[$module]->output as $output) {
            if (tep_not_null($output['title']) && tep_not_null($output['text'])) {
              $this->_data[] = array('code' => $GLOBALS[$module]->getCode(),
                                     'title' => $output['title'],
                                     'text' => $output['text'],
                                     'value' => $output['value'],
                                     'sort_order' => $GLOBALS[$module]->getSortOrder());
            }
          }
        }
      }

      return $this->_data;
    }

    function hasActive() {
      static $has_active;

      if (isset($has_active) === false) {
        $has_active = false;

        foreach ($this->_modules as $module) {
          if ($GLOBALS['osC_OrderTotal_' . $module]->getStatus() === true) {
            $has_active = true;
            break;
          }
        }
      }

      return $has_active;
    }

    function hasKeys() {
      static $has_keys;

      if (isset($has_keys) === false) {
        $has_keys = (sizeof($this->getKeys()) > 0) ? true : false;
      }

      return $has_keys;
    }

    function install() {
      global $osC_Database, $osC_Language;

      $Qinstall = $osC_Database->query('insert into :table_templates_boxes (title, code, author_name, author_www, modules_group) values (:title, :code, :author_name, :author_www, :modules_group)');
      $Qinstall->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
      $Qinstall->bindValue(':title', $this->_title);
      $Qinstall->bindValue(':code', $this->_code);
      $Qinstall->bindValue(':author_name', $this->_author_name);
      $Qinstall->bindValue(':author_www', $this->_author_www);
      $Qinstall->bindValue(':modules_group', $this->_group);
      $Qinstall->execute();

      foreach ($osC_Language->getAll() as $key => $value) {
        if (file_exists(dirname(__FILE__) . '/../languages/' . $key . '/modules/' . $this->_group . '/' . $this->_code . '.xml')) {
          foreach ($osC_Language->extractDefinitions($key . '/modules/' . $this->_group . '/' . $this->_code . '.xml') as $def) {
            $Qcheck = $osC_Database->query('select id from :table_languages_definitions where definition_key = :definition_key and content_group = :content_group and languages_id = :languages_id limit 1');
            $Qcheck->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
            $Qcheck->bindValue(':definition_key', $def['key']);
            $Qcheck->bindValue(':content_group', $def['group']);
            $Qcheck->bindInt(':languages_id', $value['id']);
            $Qcheck->execute();

            if ($Qcheck->numberOfRows() === 1) {
              $Qdef = $osC_Database->query('update :table_languages_definitions set definition_value = :definition_value where definition_key = :definition_key and content_group = :content_group and languages_id = :languages_id');
            } else {
              $Qdef = $osC_Database->query('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
            }
            $Qdef->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
            $Qdef->bindInt(':languages_id', $value['id']);
            $Qdef->bindValue(':content_group', $def['group']);
            $Qdef->bindValue(':definition_key', $def['key']);
            $Qdef->bindValue(':definition_value', $def['value']);
            $Qdef->execute();
          }
        }
      }

      osC_Cache::clear('languages');
    }

    function remove() {
      global $osC_Database, $osC_Language;

      $Qdel = $osC_Database->query('delete from :table_templates_boxes where code = :code and modules_group = :modules_group');
      $Qdel->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
      $Qdel->bindValue(':code', $this->_code);
      $Qdel->bindValue(':modules_group', $this->_group);
      $Qdel->execute();

      if ($this->hasKeys()) {
        $Qdel = $osC_Database->query('delete from :table_configuration where configuration_key in (":configuration_key")');
        $Qdel->bindTable(':table_configuration', TABLE_CONFIGURATION);
        $Qdel->bindRaw(':configuration_key', implode('", "', $this->getKeys()));
        $Qdel->execute();
      }

      if (file_exists(dirname(__FILE__) . '/../languages/' . $osC_Language->getCode() . '/modules/' . $this->_group . '/' . $this->_code . '.xml')) {
        foreach ($osC_Language->extractDefinitions($osC_Language->getCode() . '/modules/' . $this->_group . '/' . $this->_code . '.xml') as $def) {
          $Qdel = $osC_Database->query('delete from :table_languages_definitions where definition_key = :definition_key and content_group = :content_group');
          $Qdel->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
          $Qdel->bindValue(':definition_key', $def['key']);
          $Qdel->bindValue(':content_group', $def['group']);
          $Qdel->execute();
        }

        osC_Cache::clear('languages');
      }
    }

    function _usortModules($a, $b) {
      if ($GLOBALS['osC_OrderTotal_' . $a]->getSortOrder() == $GLOBALS['osC_OrderTotal_' . $b]->getSortOrder()) {
        return strnatcasecmp($GLOBALS['osC_OrderTotal_' . $a]->getTitle(), $GLOBALS['osC_OrderTotal_' . $a]->getTitle());
      }

      return ($GLOBALS['osC_OrderTotal_' . $a]->getSortOrder() < $GLOBALS['osC_OrderTotal_' . $b]->getSortOrder()) ? -1 : 1;
    }
  }
?>
