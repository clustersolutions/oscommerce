<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Module\Service;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class CategoryPath {
    var $title,
        $description,
        $uninstallable = false,
        $depends,
        $precedes;

    public function __construct() {
      $OSCOM_Language = Registry::get('Language');

      $OSCOM_Language->loadIniFile('modules/services/category_path.php');

      $this->title = OSCOM::getDef('services_category_path_title');
      $this->description = OSCOM::getDef('services_category_path_description');
    }

    public function install() {
      $data = array('title' => 'Calculate Number Of Products In Each Category',
                    'key' => 'SERVICES_CATEGORY_PATH_CALCULATE_PRODUCT_COUNT',
                    'value' => '1',
                    'description' => 'Recursively calculate how many products are in each category.',
                    'group_id' => '6',
                    'use_function' => 'osc_cfg_use_get_boolean_value',
                    'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))');

      OSCOM::callDB('Admin\InsertConfigurationParameters', $data, 'Site');
    }

    public function remove() {
      OSCOM::callDB('Admin\DeleteConfigurationParameters', $this->keys(), 'Site');
    }

    public function keys() {
      return array('SERVICES_CATEGORY_PATH_CALCULATE_PRODUCT_COUNT');
    }
  }
?>
