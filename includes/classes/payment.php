<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Payment {
    var $selected_module;

    var $_modules = array(),
        $_group = 'payment';

// class constructor
    function osC_Payment($module = '') {
      global $osC_Database, $osC_Language;

      $Qmodules = $osC_Database->query('select code from :table_templates_boxes where modules_group = "payment"');
      $Qmodules->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
      $Qmodules->setCache('modules-payment');
      $Qmodules->execute();

      while ($Qmodules->next()) {
        $this->_modules[] = $Qmodules->value('code');
      }

      $Qmodules->freeResult();

      if (empty($this->_modules) === false) {
        if ((empty($module) === false) && in_array($module, $this->_modules)) {
          $this->_modules = array($module);
          $this->selected_module = 'osC_Payment_' . $module;
        }

        $osC_Language->load('modules-payment');

        foreach ($this->_modules as $modules) {
          include('includes/modules/payment/' . $modules . '.' . substr(basename(__FILE__), (strrpos(basename(__FILE__), '.')+1)));

          $module_class = 'osC_Payment_' . $modules;

          $GLOBALS[$module_class] = new $module_class();
        }

        usort($this->_modules, array('osC_Payment', '_usortModules'));

        if ( (tep_not_null($module)) && (in_array($module, $this->_modules)) && (isset($GLOBALS['osC_Payment_' . $module]->form_action_url)) ) {
          $this->form_action_url = $GLOBALS['osC_Payment_' . $module]->form_action_url;
        }
      }
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

/* The following method is needed in the checkout_confirmation.php page
   due to a chicken and egg problem with the payment class and order class.
   The payment modules needs the order destination data for the dynamic status
   feature, and the order class needs the payment module title.
   The following method is a work-around to implementing the method in all
   payment modules available which would break the modules in the contributions
   section. This should be looked into again post 2.2.
*/
    function update_status() {
      if (is_array($this->_modules)) {
        if (isset($GLOBALS[$this->selected_module]) && is_object($GLOBALS[$this->selected_module])) {
          if (method_exists($GLOBALS[$this->selected_module], 'update_status')) {
            $GLOBALS[$this->selected_module]->update_status();
          }
        }
      }
    }

    function javascript_validation() {
      global $osC_Language;

      $js = '';
      if (is_array($this->_modules)) {
        $js = '<script type="text/javascript"><!-- ' . "\n" .
              'function check_form() {' . "\n" .
              '  var error = 0;' . "\n" .
              '  var error_message = "' . $osC_Language->get('js_error') . '";' . "\n" .
              '  var payment_value = null;' . "\n" .
              '  if (document.checkout_payment.payment.length) {' . "\n" .
              '    for (var i=0; i<document.checkout_payment.payment.length; i++) {' . "\n" .
              '      if (document.checkout_payment.payment[i].checked) {' . "\n" .
              '        payment_value = document.checkout_payment.payment[i].value;' . "\n" .
              '      }' . "\n" .
              '    }' . "\n" .
              '  } else if (document.checkout_payment.payment.checked) {' . "\n" .
              '    payment_value = document.checkout_payment.payment.value;' . "\n" .
              '  } else if (document.checkout_payment.payment.value) {' . "\n" .
              '    payment_value = document.checkout_payment.payment.value;' . "\n" .
              '  }' . "\n\n";

        foreach ($this->_modules as $module) {
          if ($GLOBALS['osC_Payment_' . $module]->getStatus() === true) {
            $js .= $GLOBALS['osC_Payment_' . $module]->javascript_validation();
          }
        }

        $js .= "\n" . '  if (payment_value == null) {' . "\n" .
               '    error_message = error_message + "' . $osC_Language->get('js_no_payment_module_selected') . '\n";' . "\n" .
               '    error = 1;' . "\n" .
               '  }' . "\n\n" .
               '  if (error == 1) {' . "\n" .
               '    alert(error_message);' . "\n" .
               '    return false;' . "\n" .
               '  } else {' . "\n" .
               '    return true;' . "\n" .
               '  }' . "\n" .
               '}' . "\n" .
               '//--></script>' . "\n";
      }

      return $js;
    }

    function selection() {
      $selection_array = array();

      foreach ($this->_modules as $module) {
        if ($GLOBALS['osC_Payment_' . $module]->getStatus() === true) {
          $selection = $GLOBALS['osC_Payment_' . $module]->selection();
          if (is_array($selection)) $selection_array[] = $selection;
        }
      }

      return $selection_array;
    }

    function pre_confirmation_check() {
      if (is_array($this->_modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->getStatus() === true) ) {
          $GLOBALS[$this->selected_module]->pre_confirmation_check();
        }
      }
    }

    function confirmation() {
      if (is_array($this->_modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->getStatus() === true) ) {
          return $GLOBALS[$this->selected_module]->confirmation();
        }
      }
    }

    function process_button() {
      if (is_array($this->_modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->getStatus() === true) ) {
          return $GLOBALS[$this->selected_module]->process_button();
        }
      }
    }

    function before_process() {
      if (is_array($this->_modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->getStatus() === true) ) {
          return $GLOBALS[$this->selected_module]->before_process();
        }
      }
    }

    function after_process() {
      if (is_array($this->_modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->getStatus() === true) ) {
          return $GLOBALS[$this->selected_module]->after_process();
        }
      }
    }

    function get_error() {
      if (is_array($this->_modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->getStatus() === true) ) {
          return $GLOBALS[$this->selected_module]->get_error();
        }
      }
    }

    function hasActionURL() {
      if (is_array($this->_modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->getStatus() === true) ) {
          if (isset($GLOBALS[$this->selected_module]->form_action_url) && (empty($GLOBALS[$this->selected_module]->form_action_url) === false)) {
            return true;
          }
        }
      }

      return false;
    }

    function getActionURL() {
      return $GLOBALS[$this->selected_module]->form_action_url;
    }

    function hasActive() {
      static $has_active;

      if (isset($has_active) === false) {
        $has_active = false;

        foreach ($this->_modules as $module) {
          if ($GLOBALS['osC_Payment_' . $module]->getStatus() === true) {
            $has_active = true;
            break;
          }
        }
      }

      return $has_active;
    }

    function numberOfActive() {
      static $active;

      if (isset($active) === false) {
        $active = 0;

        foreach ($this->_modules as $module) {
          if ($GLOBALS['osC_Payment_' . $module]->getStatus() === true) {
            $active++;
          }
        }
      }

      return $active;
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
      if ($GLOBALS['osC_Payment_' . $a]->getSortOrder() == $GLOBALS['osC_Payment_' . $b]->getSortOrder()) {
        return strnatcasecmp($GLOBALS['osC_Payment_' . $a]->getTitle(), $GLOBALS['osC_Payment_' . $a]->getTitle());
      }

      return ($GLOBALS['osC_Payment_' . $a]->getSortOrder() < $GLOBALS['osC_Payment_' . $b]->getSortOrder()) ? -1 : 1;
    }
  }
?>
