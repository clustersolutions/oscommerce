<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Configuration_Admin {
    function getGroupTitle($id) {
      global $osC_Database;

      $Qcg = $osC_Database->query('select configuration_group_title from :table_configuration_group where configuration_group_id = :configuration_group_id');
      $Qcg->bindTable(':table_configuration_group', TABLE_CONFIGURATION_GROUP);
      $Qcg->bindInt(':configuration_group_id', $id);
      $Qcg->execute();

      $result = $Qcg->value('configuration_group_title');

      $Qcg->freeResult();

      return $result;
    }

    function getData($id) {
      global $osC_Database;

      $Qcfg = $osC_Database->query('select * from :table_configuration where configuration_id = :configuration_id');
      $Qcfg->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qcfg->bindInt(':configuration_id', $id);
      $Qcfg->execute();

      $result = $Qcfg->toArray();

      $Qcfg->freeResult();

      return $result;
    }

    function save($id, $value) {
      global $osC_Database;

      $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value, last_modified = now() where configuration_id = :configuration_id');
      $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qupdate->bindValue(':configuration_value', $value);
      $Qupdate->bindInt(':configuration_id', $id);
      $Qupdate->execute();

      if ( $Qupdate->affectedRows() ) {
        osC_Cache::clear('configuration');

        return true;
      }

      return false;
    }
  }
?>
