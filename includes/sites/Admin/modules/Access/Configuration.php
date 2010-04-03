<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Access_Configuration extends osC_Access {
    var $_module = 'Configuration',
        $_group = 'configuration',
        $_icon = 'configure.png',
        $_title,
        $_sort_order = 200;

    public function __construct() {
      $this->_title = __('access_configuration_title');

      $this->_subgroups = array();

      $Qgroups = OSCOM_Registry::get('Database')->query('select configuration_group_id, configuration_group_title from :table_configuration_group where visible = 1 order by sort_order, configuration_group_title');
      $Qgroups->bindTable(':table_configuration_group', TABLE_CONFIGURATION_GROUP);
      $Qgroups->execute();

      while ($Qgroups->next()) {
        $this->_subgroups[] = array('icon' => 'configure.png',
                                    'title' => $Qgroups->value('configuration_group_title'),
                                    'identifier' => 'gID=' . $Qgroups->valueInt('configuration_group_id'));
      }
    }
  }
?>
