<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Configuration_Admin {
    public static function get($id) {
      global $osC_Database;

      $Qcfg = $osC_Database->query('select * from :table_configuration where configuration_id = :configuration_id');
      $Qcfg->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qcfg->bindInt(':configuration_id', $id);
      $Qcfg->execute();

      $result = $Qcfg->toArray();

      $Qcfg->freeResult();

      return $result;
    }

    public static function getAll($group_id) {
      global $osC_Database;

      $Qcfg = $osC_Database->query('select * from :table_configuration where configuration_group_id = :configuration_group_id order by sort_order');
      $Qcfg->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qcfg->bindInt(':configuration_group_id', $group_id);
      $Qcfg->execute();

      $result = array('entries' => array());

      while ( $Qcfg->next() ) {
        $result['entries'][] = $Qcfg->toArray();

        if ( !osc_empty($Qcfg->value('use_function')) ) {
          $result['entries'][sizeof($result['entries'])-1]['configuration_value'] = osc_call_user_func($Qcfg->value('use_function'), $Qcfg->value('configuration_value'));
        }
      }

      $result['total'] = $Qcfg->numberOfRows();

      $Qcfg->freeResult();

      return $result;
    }

    public static function find($search) {
      global $osC_Database;

      $in_group = array();

      foreach ( osc_toObjectInfo(self::getAllGroups())->get('entries') as $group ) {
        $in_group[] = $group['configuration_group_id'];
      }

      $result = array('entries' => array());

      $Qcfg = $osC_Database->query('select * from :table_configuration where (configuration_key like :configuration_key or configuration_value like :configuration_value) and configuration_group_id in (:configuration_group_id) order by configuration_key');
      $Qcfg->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qcfg->bindValue(':configuration_key', '%' . $search . '%');
      $Qcfg->bindValue(':configuration_value', '%' . $search . '%');
      $Qcfg->bindRaw(':configuration_group_id', implode(',', $in_group));
      $Qcfg->execute();

      while ( $Qcfg->next() ) {
        $result['entries'][] = $Qcfg->toArray();

        if ( !osc_empty($Qcfg->value('use_function')) ) {
          $result['entries'][sizeof($result['entries'])-1]['configuration_value'] = osc_call_user_func($Qcfg->value('use_function'), $Qcfg->value('configuration_value'));
        }
      }

      $result['total'] = $Qcfg->numberOfRows();

      $Qcfg->freeResult();

      return $result;
    }

    public static function save($parameter) {
      global $osC_Database;

      $Qcfg = $osC_Database->query('select configuration_id from :table_configuration where configuration_key = :configuration_key');
      $Qcfg->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qcfg->bindValue(':configuration_key', key($parameter));
      $Qcfg->execute();

      if ( $Qcfg->numberOfRows() === 1 ) {
        $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value, last_modified = now() where configuration_key = :configuration_key');
        $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
        $Qupdate->bindValue(':configuration_value', $parameter[key($parameter)]);
        $Qupdate->bindValue(':configuration_key', key($parameter));
        $Qupdate->setLogging($_SESSION['module'], $Qcfg->valueInt('configuration_id'));
        $Qupdate->execute();

        if ( $Qupdate->affectedRows() ) {
          osC_Cache::clear('configuration');

          return true;
        }
      }

      return false;
    }

    public static function getAllGroups() {
      global $osC_Database;

      $Qgroups = $osC_Database->query('select * from :table_configuration_group where visible = 1 order by sort_order, configuration_group_title');
      $Qgroups->bindTable(':table_configuration_group', TABLE_CONFIGURATION_GROUP);
      $Qgroups->execute();

      $result = array('entries' => array());

      while ( $Qgroups->next() ) {
        $result['entries'][] = $Qgroups->toArray();
      }

      $result['total'] = $Qgroups->numberOfRows();

      $Qgroups->freeResult();

      return $result;
    }

    public static function getGroupTitle($id) {
      global $osC_Database;

      $Qcg = $osC_Database->query('select configuration_group_title from :table_configuration_group where configuration_group_id = :configuration_group_id');
      $Qcg->bindTable(':table_configuration_group', TABLE_CONFIGURATION_GROUP);
      $Qcg->bindInt(':configuration_group_id', $id);
      $Qcg->execute();

      $result = $Qcg->value('configuration_group_title');

      $Qcg->freeResult();

      return $result;
    }
  }
?>
