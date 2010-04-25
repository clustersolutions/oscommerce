<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Application_Configuration extends OSCOM_Site_Admin_ApplicationAbstract {
    protected $_group = 'configuration';
    protected $_icon = 'configuration.png';
    protected $_sort_order = 200;

    protected function initialize() {
      $this->_title = OSCOM::getDef('app_title');

      $Qgroups = OSCOM_Registry::get('Database')->query('select configuration_group_id, configuration_group_title from :table_configuration_group where visible = 1 order by sort_order, configuration_group_title');
      $Qgroups->execute();

      while ($Qgroups->next()) {
        $this->_subgroups[] = array('icon' => 'configuration.png',
                                    'title' => $Qgroups->value('configuration_group_title'),
                                    'identifier' => 'gID=' . $Qgroups->valueInt('configuration_group_id'));
      }
    }

    protected function process() {
      $this->_page_title = OSCOM::getDef('heading_title');

      if ( !isset($_GET['gID']) || !is_numeric($_GET['gID']) ) {
        $_GET['gID'] = 1;
      }
    }
  }
?>
