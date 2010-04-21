<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Application_ZoneGroups_ZoneGroups {
    public static function get($id, $key = null) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      $Qzones = $OSCOM_Database->query('select * from :table_geo_zones where geo_zone_id = :geo_zone_id');
      $Qzones->bindInt(':geo_zone_id', $id);
      $Qzones->execute();

      $data = array_merge($Qzones->toArray(), array('total_entries' => self::numberOfEntries($id)));

      if ( empty($key) ) {
        return $data;
      } else {
        return $data[$key];
      }
    }

    public static function getAll($pageset = 1) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qgroups = $OSCOM_Database->query('select SQL_CALC_FOUND_ROWS * from :table_geo_zones order by geo_zone_name');

      if ( $pageset !== -1 ) {
        $Qgroups->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qgroups->execute();

      while ( $Qgroups->next() ) {
        $result['entries'][] = array_merge($Qgroups->toArray(), array('total_entries' => self::numberOfEntries($Qgroups->valueInt('geo_zone_id'))));
      }

      $result['total'] = $Qgroups->getBatchSize();

      return $result;
    }

    public static function find($search, $pageset = 1) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qgroups = $OSCOM_Database->query('select SQL_CALC_FOUND_ROWS gz.* from :table_geo_zones gz, :table_zones_to_geo_zones z2gz, :table_countries c, :table_zones z where gz.geo_zone_id = z2gz.geo_zone_id and z2gz.zone_country_id = c.countries_id and z2gz.zone_id = z.zone_id and (gz.geo_zone_name like :geo_zone_name or gz.geo_zone_description like :geo_zone_description or c.countries_name like :countries_name or z.zone_name like :zone_name) group by gz.geo_zone_id order by gz.geo_zone_name');
      $Qgroups->bindValue(':geo_zone_name', '%' . $search . '%');
      $Qgroups->bindValue(':geo_zone_description', '%' . $search . '%');
      $Qgroups->bindValue(':countries_name', '%' . $search . '%');
      $Qgroups->bindValue(':zone_name', '%' . $search . '%');

      if ( $pageset !== -1 ) {
        $Qgroups->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qgroups->execute();

      while ( $Qgroups->next() ) {
        $result['entries'][] = array_merge($Qgroups->toArray(), array('total_entries' => self::numberOfEntries($Qgroups->valueInt('geo_zone_id'))));
      }

      $result['total'] = $Qgroups->getBatchSize();

      return $result;
    }

    public static function save($id = null, $data) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      if ( is_numeric($id) ) {
        $Qzone = $OSCOM_Database->query('update :table_geo_zones set geo_zone_name = :geo_zone_name, geo_zone_description = :geo_zone_description, last_modified = now() where geo_zone_id = :geo_zone_id');
        $Qzone->bindInt(':geo_zone_id', $id);
      } else {
        $Qzone = $OSCOM_Database->query('insert into :table_geo_zones (geo_zone_name, geo_zone_description, date_added) values (:geo_zone_name, :geo_zone_description, now())');
      }

      $Qzone->bindValue(':geo_zone_name', $data['zone_name']);
      $Qzone->bindValue(':geo_zone_description', $data['zone_description']);
      $Qzone->setLogging(null, $id);
      $Qzone->execute();

      return !$osC_Database->isError();
    }

    public static function delete($id) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      $Qzone = $OSCOM_Database->query('delete from :table_geo_zones where geo_zone_id = :geo_zone_id');
      $Qzone->bindInt(':geo_zone_id', $id);
      $Qzone->setLogging(null, $id);
      $Qzone->execute();

      return !$osC_Database->isError();
    }

    public static function numberOfEntries($id) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      $Qentries = $OSCOM_Database->query('select count(*) as total from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id');
      $Qentries->bindInt(':geo_zone_id', $id);
      $Qentries->execute();

      return $Qentries->valueInt('total');
    }

    public static function hasTaxRate($id) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      $Qcheck = $OSCOM_Database->query('select tax_zone_id from :table_tax_rates where tax_zone_id = :tax_zone_id limit 1');
      $Qcheck->bindInt(':tax_zone_id', $id);
      $Qcheck->execute();

      return ( $Qcheck->numberOfRows() === 1 );
    }

    public static function numberOfTaxRates($id) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      $Qtotal = $OSCOM_Database->query('select count(*) as total from :table_tax_rates where tax_zone_id = :tax_zone_id');
      $Qtotal->bindInt(':tax_zone_id', $id);
      $Qtotal->execute();

      return $Qtotal->valueInt('total');
    }

    public static function getEntry($id) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      $Qentries = $OSCOM_Database->query('select z2gz.*, c.countries_name, z.zone_name from :table_zones_to_geo_zones z2gz left join :table_countries c on (z2gz.zone_country_id = c.countries_id) left join :table_zones z on (z2gz.zone_id = z.zone_id) where z2gz.association_id = :association_id');
      $Qentries->bindInt(':association_id', $id);
      $Qentries->execute();

      $data = $Qentries->toArray();

      if ( empty($data['countries_name']) ) {
        $data['countries_name'] = OSCOM::getDef('all_countries');
      }

      if ( empty($data['zone_name']) ) {
        $data['zone_name'] = OSCOM::getDef('all_zones');
      }

      return $data;
    }

    public static function getAllEntries($group_id) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      $result = array('entries' => array());

      $Qentries = $OSCOM_Database->query('select z2gz.*, c.countries_name, z.zone_name from :table_zones_to_geo_zones z2gz left join :table_countries c on (z2gz.zone_country_id = c.countries_id) left join :table_zones z on (z2gz.zone_id = z.zone_id) where z2gz.geo_zone_id = :geo_zone_id order by c.countries_name, z.zone_name');
      $Qentries->bindInt(':geo_zone_id', $group_id);
      $Qentries->execute();

      while ( $Qentries->next() ) {
        $result['entries'][] = $Qentries->toArray();
      }

      $result['total'] = $Qentries->numberOfRows();

      return $result;
    }

    public static function findEntries($search, $group_id) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      $result = array('entries' => array());

      $Qentries = $OSCOM_Database->query('select SQL_CALC_FOUND_ROWS z2gz.*, c.countries_name, z.zone_name from :table_zones_to_geo_zones z2gz, :table_countries c, :table_zones z where z2gz.geo_zone_id = :geo_zone_id and z2gz.zone_country_id = c.countries_id and z2gz.zone_id = z.zone_id and (c.countries_name like :countries_name or z.zone_name like :zone_name) order by c.countries_name, z.zone_name');
      $Qentries->bindInt(':geo_zone_id', $group_id);
      $Qentries->bindValue(':countries_name', '%' . $search . '%');
      $Qentries->bindValue(':zone_name', '%' . $search . '%');
      $Qentries->execute();

      while ( $Qentries->next() ) {
        $result['entries'][] = $Qentries->toArray();
      }

      $result['total'] = $Qentries->numberOfRows();

      return $result;
    }

    public static function saveEntry($id = null, $data) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      if ( is_numeric($id) ) {
        $Qentry = $OSCOM_Database->query('update :table_zones_to_geo_zones set zone_country_id = :zone_country_id, zone_id = :zone_id, last_modified = now() where association_id = :association_id');
        $Qentry->bindInt(':association_id', $id);
      } else {
        $Qentry = $OSCOM_Database->query('insert into :table_zones_to_geo_zones (zone_country_id, zone_id, geo_zone_id, date_added) values (:zone_country_id, :zone_id, :geo_zone_id, now())');
        $Qentry->bindInt(':geo_zone_id', $data['group_id']);
      }
      $Qentry->bindInt(':zone_country_id', $data['country_id']);
      $Qentry->bindInt(':zone_id', $data['zone_id']);
      $Qentry->setLogging(null, $id);
      $Qentry->execute();

      return !$osC_Database->isError();
    }

    public static function deleteEntry($id) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      $Qentry = $OSCOM_Database->query('delete from :table_zones_to_geo_zones where association_id = :association_id');
      $Qentry->bindInt(':association_id', $id);
      $Qentry->setLogging(null, $id);
      $Qentry->execute();

      return !$osC_Database->isError();
    }
  }
?>
