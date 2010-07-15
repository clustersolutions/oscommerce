<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Module\Service;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Currencies {
    var $title,
        $description,
        $uninstallable = false,
        $depends = 'Language',
        $precedes;

    public function __construct() {
      $OSCOM_Language = Registry::get('Language');

      $OSCOM_Language->loadIniFile('modules/services/currencies.php');

      $this->title = OSCOM::getDef('services_currencies_title');
      $this->description = OSCOM::getDef('services_currencies_description');
    }

    public function install() {
      $OSCOM_Database = Registry::get('Database');

      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Use Default Language Currency', 'USE_DEFAULT_LANGUAGE_CURRENCY', '-1', 'Automatically use the currency set with the language (eg, German->Euro).', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
    }

    public function remove() {
      $OSCOM_Database = Registry::get('Database');

      $OSCOM_Database->simpleQuery("delete from " . DB_TABLE_PREFIX . "configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    public function keys() {
      return array('USE_DEFAULT_LANGUAGE_CURRENCY');
    }
  }
?>
