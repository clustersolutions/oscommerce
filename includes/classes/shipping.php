<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Shipping {
    var $_modules = array(),
        $_selected_module,
        $_quotes = array(),
        $_keys,
        $_group = 'shipping';

// class constructor
    function osC_Shipping($module = '') {
      global $osC_Database, $osC_Language;

      if (isset($_SESSION['osC_Shipping_data']) === false) {
        $_SESSION['osC_Shipping_data'] = array('quotes' => array(),
                                               'cartID' => null);
      }

      $this->_quotes =& $_SESSION['osC_Shipping_data']['quotes'];
      $this->_cartID =& $_SESSION['osC_Shipping_data']['cartID'];

      $Qmodules = $osC_Database->query('select code from :table_templates_boxes where modules_group = "shipping"');
      $Qmodules->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
      $Qmodules->setCache('modules-shipping');
      $Qmodules->execute();

      while ($Qmodules->next()) {
        $this->_modules[] = $Qmodules->value('code');
      }

      $Qmodules->freeResult();

      if (empty($this->_modules) === false) {
        if ((empty($module) === false) && in_array(substr($module, 0, strpos($module, '_')), $this->_modules)) {
          $this->_selected_module = $module;
          $this->_modules = array(substr($module, 0, strpos($module, '_')));
        }

        $osC_Language->load('modules-shipping');

        foreach ($this->_modules as $module) {
          $module_class = 'osC_Shipping_' . $module;

          if (class_exists($module_class) === false) {
            include('includes/modules/shipping/' . $module . '.' . substr(basename(__FILE__), (strrpos(basename(__FILE__), '.')+1)));
          }

          $GLOBALS[$module_class] = new $module_class();
          $GLOBALS[$module_class]->initialize();
        }

        usort($this->_modules, array('osC_Shipping', '_usortModules'));
      }

      $this->_calculate();
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

    function hasQuotes() {
      return !empty($this->_quotes);
    }

    function numberOfQuotes() {
      $total_quotes = 0;

      foreach ($this->_quotes as $quotes) {
        $total_quotes += sizeof($quotes['methods']);
      }

      return $total_quotes;
    }

    function getQuotes() {
      return $this->_quotes;
    }

    function getQuote($module = '') {
      if (empty($module)) {
        $module = $this->_selected_module;
      }

      list($module_id, $method_id) = explode('_', $module);

      $rate = array();

      foreach ($this->_quotes as $quote) {
        if ($quote['id'] == $module_id) {
          foreach ($quote['methods'] as $method) {
            if ($method['id'] == $method_id) {
              $rate = array('id' => $module,
                            'title' => $quote['module'] . ((empty($method['title']) === false) ? ' (' . $method['title'] . ')' : ''),
                            'cost' => $method['cost'],
                            'tax_class_id' => $quote['tax_class_id']);

              break 2;
            }
          }
        }
      }

      return $rate;
    }

    function getCheapestQuote() {
      $rate = array();

      foreach ($this->_quotes as $quote) {
        foreach ($quote['methods'] as $method) {
          if (empty($rate) || ($method['cost'] < $rate['cost'])) {
            $rate = array('id' => $quote['id'] . '_' . $method['id'],
                          'title' => $quote['module'] . ((empty($method['title']) === false) ? ' (' . $method['title'] . ')' : ''),
                          'cost' => $method['cost'],
                          'tax_class_id' => $quote['tax_class_id'],
                          'is_cheapest' => true);
          }
        }
      }

      return $rate;
    }

    function hasActive() {
      static $has_active;

      if (isset($has_active) === false) {
        $has_active = false;

        foreach ($this->_modules as $module) {
          if ($GLOBALS['osC_Shipping_' . $module]->getStatus() === true) {
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

    function _calculate() {
      global $osC_ShoppingCart;

      if ($this->_cartID != $osC_ShoppingCart->getCartID()) {
        $this->_cartID = $osC_ShoppingCart->getCartID();

        $this->_quotes = array();

        if (is_array($this->_modules)) {
          $include_quotes = array();

          if (defined('MODULE_SHIPPING_FREE_STATUS') && (MODULE_SHIPPING_FREE_STATUS == 'True') && ($GLOBALS['osC_Shipping_free']->getStatus() === true)) {
            $include_quotes[] = 'osC_Shipping_free';
          } else {
            foreach ($this->_modules as $module) {
              if ($GLOBALS['osC_Shipping_' . $module]->getStatus() === true) {
                $include_quotes[] = 'osC_Shipping_' . $module;
              }
            }
          }

          foreach ($include_quotes as $module) {
            $quotes = $GLOBALS[$module]->quote();

            if (is_array($quotes)) {
              $this->_quotes[] = $quotes;
            }
          }
        }
      }
    }

    function _usortModules($a, $b) {
      if ($GLOBALS['osC_Shipping_' . $a]->getSortOrder() == $GLOBALS['osC_Shipping_' . $b]->getSortOrder()) {
        return strnatcasecmp($GLOBALS['osC_Shipping_' . $a]->getTitle(), $GLOBALS['osC_Shipping_' . $a]->getTitle());
      }

      return ($GLOBALS['osC_Shipping_' . $a]->getSortOrder() < $GLOBALS['osC_Shipping_' . $b]->getSortOrder()) ? -1 : 1;
    }
  }
?>
