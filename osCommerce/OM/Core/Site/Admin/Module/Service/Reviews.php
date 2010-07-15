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

  class Reviews {
    var $title,
        $description,
        $uninstallable = true,
        $depends,
        $precedes;

    public function __construct() {
      $OSCOM_Language = Registry::get('Language');

      $OSCOM_Language->loadIniFile('modules/services/reviews.php');

      $this->title = OSCOM::getDef('services_reviews_title');
      $this->description = OSCOM::getDef('services_reviews_description');
    }

    public function install() {
      $OSCOM_Database = Registry::get('Database');

      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('New Reviews', 'MAX_DISPLAY_NEW_REVIEWS', '6', 'Maximum number of new reviews to display', '6', '0', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Review Level', 'SERVICE_REVIEW_ENABLE_REVIEWS', '1', 'Customer level required to write a review.', '6', '0', 'osc_cfg_set_boolean_value(array(\'0\', \'1\', \'2\'))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Moderate Reviews', 'SERVICE_REVIEW_ENABLE_MODERATION', '-1', 'Should reviews be approved by store admin.', '6', '0', 'osc_cfg_set_boolean_value(array(\'-1\', \'0\', \'1\'))', now())");
    }

    public function remove() {
      $OSCOM_Database = Registry::get('Database');

      $OSCOM_Database->simpleQuery("delete from " . DB_TABLE_PREFIX . "configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    public function keys() {
      return array('MAX_DISPLAY_NEW_REVIEWS',
                   'SERVICE_REVIEW_ENABLE_REVIEWS',
                   'SERVICE_REVIEW_ENABLE_MODERATION');
    }
  }
?>
