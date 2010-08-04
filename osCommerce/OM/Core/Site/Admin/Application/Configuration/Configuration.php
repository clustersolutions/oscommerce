<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Cache;

  class Configuration {
    public static function get($id, $key = null) {
      $OSCOM_Database = Registry::get('Database');

      $result = false;

      $Qgroup = $OSCOM_Database->query('select * from :table_configuration_group where configuration_group_id = :configuration_group_id');
      $Qgroup->bindInt(':configuration_group_id', $id);
      $Qgroup->execute();

      if ( $Qgroup->numberOfRows() === 1 ) {
        $Qentries = $OSCOM_Database->query('select count(*) as total_entries from :table_configuration where configuration_group_id = :configuration_group_id');
        $Qentries->bindInt(':configuration_group_id', $Qgroup->valueInt('configuration_group_id'));
        $Qentries->execute();

        $result = array_merge($Qgroup->toArray(), $Qentries->toArray());

        if ( !empty($key) && isset($result[$key]) ) {
          $result = $result[$key];
        }
      }

      return $result;
    }

    public static function getAll() {
      $OSCOM_Database = Registry::get('Database');

      $result = array();

      $Qgroups = $OSCOM_Database->query('select cg.configuration_group_id, cg.configuration_group_title, count(c.configuration_id) as total_entries from :table_configuration_group cg, :table_configuration c where cg.visible = 1 and cg.configuration_group_id = c.configuration_group_id group by cg.configuration_group_id order by cg.sort_order, cg.configuration_group_title');
      $Qgroups->execute();

      $result['entries'] = $Qgroups->getAll();

      $result['total'] = $Qgroups->numberOfRows();

      return $result;
    }

    public static function find($search) {
      $OSCOM_Database = Registry::get('Database');

      $result = array();

      $Qgroups = $OSCOM_Database->query('select distinct cg.configuration_group_id, cg.configuration_group_title, count(c.configuration_id) as total_entries from :table_configuration_group cg, :table_configuration c where (c.configuration_key like :configuration_key or c.configuration_value like :configuration_value) and c.configuration_group_id = cg.configuration_group_id and cg.visible = 1 group by cg.configuration_group_id order by cg.sort_order, cg.configuration_group_title');
      $Qgroups->bindValue(':configuration_key', '%' . $search . '%');
      $Qgroups->bindValue(':configuration_value', '%' . $search . '%');
      $Qgroups->execute();

      $result['entries'] = $Qgroups->getAll();

      $result['total'] = $Qgroups->numberOfRows();

      return $result;
    }

    public static function getEntry($id) {
      $OSCOM_Database = Registry::get('Database');

      $Qcfg = $OSCOM_Database->query('select * from :table_configuration where configuration_id = :configuration_id');
      $Qcfg->bindInt(':configuration_id', $id);
      $Qcfg->execute();

      $result = $Qcfg->toArray();

      return $result;
    }

    public static function getAllEntries($group_id) {
      $OSCOM_Database = Registry::get('Database');

      $Qcfg = $OSCOM_Database->query('select * from :table_configuration where configuration_group_id = :configuration_group_id order by sort_order');
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

      return $result;
    }

    public static function findEntries($search, $group_id) {
      $OSCOM_Database = Registry::get('Database');

      $result = array('entries' => array());

      $Qcfg = $OSCOM_Database->query('select * from :table_configuration where configuration_group_id = :configuration_group_id and (configuration_key like :configuration_key or configuration_value like :configuration_value) order by sort_order');
      $Qcfg->bindInt(':configuration_group_id', $group_id);
      $Qcfg->bindValue(':configuration_key', '%' . $search . '%');
      $Qcfg->bindValue(':configuration_value', '%' . $search . '%');
      $Qcfg->execute();

      while ( $Qcfg->next() ) {
        $result['entries'][] = $Qcfg->toArray();

        if ( !osc_empty($Qcfg->value('use_function')) ) {
          $result['entries'][sizeof($result['entries'])-1]['configuration_value'] = osc_call_user_func($Qcfg->value('use_function'), $Qcfg->value('configuration_value'));
        }
      }

      $result['total'] = $Qcfg->numberOfRows();

      return $result;
    }

    public static function saveEntry($parameter) {
      $OSCOM_Database = Registry::get('Database');

      $Qcfg = $OSCOM_Database->query('select configuration_id from :table_configuration where configuration_key = :configuration_key');
      $Qcfg->bindValue(':configuration_key', key($parameter));
      $Qcfg->execute();

      if ( $Qcfg->numberOfRows() === 1 ) {
        $Qupdate = $OSCOM_Database->query('update :table_configuration set configuration_value = :configuration_value, last_modified = now() where configuration_key = :configuration_key');
        $Qupdate->bindValue(':configuration_value', $parameter[key($parameter)]);
        $Qupdate->bindValue(':configuration_key', key($parameter));
        $Qupdate->setLogging(null, $Qcfg->valueInt('configuration_id'));
        $Qupdate->execute();

        if ( $Qupdate->affectedRows() ) {
          Cache::clear('configuration');

          return true;
        }
      }

      return false;
    }
  }
?>
