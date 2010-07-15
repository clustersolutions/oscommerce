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

  class Session {
    var $title,
        $description,
        $uninstallable = false,
        $depends,
        $precedes;

    public function __construct() {
      $OSCOM_Language = Registry::get('Language');

      $OSCOM_Language->loadIniFile('modules/services/session.php');

      $this->title = OSCOM::getDef('services_session_title');
      $this->description = OSCOM::getDef('services_session_description');
    }

    public function install() {
      $OSCOM_Database = Registry::get('Database');

      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Session Expiration Time', 'SERVICE_SESSION_EXPIRATION_TIME', '30', 'The time (in minutes) to keep sessions active for. A value of 0 means until the browser is closed.', '6', '0', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Force Cookie Usage', 'SERVICE_SESSION_FORCE_COOKIE_USAGE', '-1', 'Only start a session when cookies are enabled.', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Block Search Engine Spiders', 'SERVICE_SESSION_BLOCK_SPIDERS', '-1', 'Block search engine spider robots from starting a session.', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Check SSL Session ID', 'SERVICE_SESSION_CHECK_SSL_SESSION_ID', '-1', 'Check the SSL_SESSION_ID on every secure HTTPS page request.', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Check User Agent', 'SERVICE_SESSION_CHECK_USER_AGENT', '-1', 'Check the browser user agent on every page request.', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Check IP Address', 'SERVICE_SESSION_CHECK_IP_ADDRESS', '-1', 'Check the IP address on every page request.', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Regenerate Session ID', 'SERVICE_SESSION_REGENERATE_ID', '-1', 'Regenerate the session ID when a customer logs on or creates an account.', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
    }

    public function remove() {
      $OSCOM_Database = Registry::get('Database');

      $osC_Database->simpleQuery("delete from " . DB_TABLE_PREFIX . "configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    public function keys() {
      return array('SERVICE_SESSION_EXPIRATION_TIME',
                   'SERVICE_SESSION_FORCE_COOKIE_USAGE',
                   'SERVICE_SESSION_BLOCK_SPIDERS',
                   'SERVICE_SESSION_CHECK_SSL_SESSION_ID',
                   'SERVICE_SESSION_CHECK_USER_AGENT',
                   'SERVICE_SESSION_CHECK_IP_ADDRESS',
                   'SERVICE_SESSION_REGENERATE_ID');
    }
  }
?>
