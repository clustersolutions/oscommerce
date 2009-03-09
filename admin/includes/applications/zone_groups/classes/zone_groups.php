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

  class osC_ZoneGroups_Admin {
    public static function get($id, $key = null) {
      global $osC_Database;

      $Qzones = $osC_Database->query('select * from :table_geo_zones where geo_zone_id = :geo_zone_id');
      $Qzones->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
      $Qzones->bindInt(':geo_zone_id', $id);
      $Qzones->execute();

      $data = array_merge($Qzones->toArray(), array('total_entries' => self::numberOfEntries($id)));

      $Qzones->freeResult();

      if ( empty($key) ) {
        return $data;
      } else {
        return $data[$key];
      }
    }

    public static function getAll($pageset = 1) {
      global $osC_Database;

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qgroups = $osC_Database->query('select SQL_CALC_FOUND_ROWS * from :table_geo_zones order by geo_zone_name');
      $Qgroups->bindTable(':table_geo_zones', TABLE_GEO_ZONES);

      if ( $pageset !== -1 ) {
        $Qgroups->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qgroups->execute();

      while ( $Qgroups->next() ) {
        $result['entries'][] = array_merge($Qgroups->toArray(), array('total_entries' => self::numberOfEntries($Qgroups->valueInt('geo_zone_id'))));
      }

      $result['total'] = $Qgroups->getBatchSize();

      $Qgroups->freeResult();

      return $result;
    }

    public static function find($search, $pageset = 1) {
      global $osC_Database;

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qgroups = $osC_Database->query('select SQL_CALC_FOUND_ROWS gz.* from :table_geo_zones gz, :table_zones_to_geo_zones z2gz, :table_countries c, :table_zones z where gz.geo_zone_id = z2gz.geo_zone_id and z2gz.zone_country_id = c.countries_id and z2gz.zone_id = z.zone_id and (gz.geo_zone_name like :geo_zone_name or gz.geo_zone_description like :geo_zone_description or c.countries_name like :countries_name or z.zone_name like :zone_name) group by gz.geo_zone_id order by gz.geo_zone_name');
      $Qgroups->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
      $Qgroups->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
      $Qgroups->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qgroups->bindTable(':table_zones', TABLE_ZONES);
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

      $Qgroups->freeResult();

      return $result;
    }

    public static function save($id = null, $data) {
      global $osC_Database;

      if ( is_numeric($id) ) {
        $Qzone = $osC_Database->query('update :table_geo_zones set geo_zone_name = :geo_zone_name, geo_zone_description = :geo_zone_description, last_modified = now() where geo_zone_id = :geo_zone_id');
        $Qzone->bindInt(':geo_zone_id', $id);
      } else {
        $Qzone = $osC_Database->query('insert into :table_geo_zones (geo_zone_name, geo_zone_description, date_added) values (:geo_zone_name, :geo_zone_description, now())');
      }

      $Qzone->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
      $Qzone->bindValue(':geo_zone_name', $data['zone_name']);
      $Qzone->bindValue(':geo_zone_description', $data['zone_description']);
      $Qzone->setLogging($_SESSION['module'], $id);
      $Qzone->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }

    public static function delete($id) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      $Qentry = $osC_Database->query('delete from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id');
      $Qentry->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
      $Qentry->bindInt(':geo_zone_id', $id);
      $Qentry->setLogging($_SESSION['module'], $id);
      $Qentry->execute();

      if ( !$osC_Database->isError() ) {
        $Qzone = $osC_Database->query('delete from :table_geo_zones where geo_zone_id = :geo_zone_id');
        $Qzone->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
        $Qzone->bindInt(':geo_zone_id', $id);
        $Qzone->setLogging($_SESSION['module'], $id);
        $Qzone->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        }
      } else {
        $error = true;
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    public static function numberOfEntries($id) {
      global $osC_Database;

      $Qentries = $osC_Database->query('select count(*) as total from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id');
      $Qentries->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
      $Qentries->bindInt(':geo_zone_id', $id);
      $Qentries->execute();

      return $Qentries->valueInt('total');
    }

    public static function hasTaxRate($id) {
      global $osC_Database;

      $Qcheck = $osC_Database->query('select tax_zone_id from :table_tax_rates where tax_zone_id = :tax_zone_id limit 1');
      $Qcheck->bindTable(':table_tax_rates', TABLE_TAX_RATES);
      $Qcheck->bindInt(':tax_zone_id', $id);
      $Qcheck->execute();

      return ( $Qcheck->numberOfRows() === 1 );
    }

    public static function numberOfTaxRates($id) {
      global $osC_Database;

      $Qtotal = $osC_Database->query('select count(*) as total from :table_tax_rates where tax_zone_id = :tax_zone_id');
      $Qtotal->bindTable(':table_tax_rates', TABLE_TAX_RATES);
      $Qtotal->bindInt(':tax_zone_id', $id);
      $Qtotal->execute();

      return $Qtotal->valueInt('total');
    }

    public static function getEntry($id) {
      global $osC_Database, $osC_Language;

      $Qentries = $osC_Database->query('select z2gz.*, c.countries_name, z.zone_name from :table_zones_to_geo_zones z2gz left join :table_countries c on (z2gz.zone_country_id = c.countries_id) left join :table_zones z on (z2gz.zone_id = z.zone_id) where z2gz.association_id = :association_id');
      $Qentries->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
      $Qentries->bindTable(':table_zones', TABLE_ZONES);
      $Qentries->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qentries->bindInt(':association_id', $id);
      $Qentries->execute();

      $data = $Qentries->toArray();

      if ( empty($data['countries_name']) ) {
        $data['countries_name'] = $osC_Language->get('all_countries');
      }

      if ( empty($data['zone_name']) ) {
        $data['zone_name'] = $osC_Language->get('all_zones');
      }

      $Qentries->freeResult();

      return $data;
    }

    public static function getAllEntries($group_id) {
      global $osC_Database;

      $result = array('entries' => array());

      $Qentries = $osC_Database->query('select z2gz.*, c.countries_name, z.zone_name from :table_zones_to_geo_zones z2gz left join :table_countries c on (z2gz.zone_country_id = c.countries_id) left join :table_zones z on (z2gz.zone_id = z.zone_id) where z2gz.geo_zone_id = :geo_zone_id order by c.countries_name, z.zone_name');
      $Qentries->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
      $Qentries->bindTable(':table_zones', TABLE_ZONES);
      $Qentries->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qentries->bindInt(':geo_zone_id', $group_id);
      $Qentries->execute();

      while ( $Qentries->next() ) {
        $result['entries'][] = $Qentries->toArray();
      }

      $result['total'] = $Qentries->numberOfRows();

      $Qentries->freeResult();

      return $result;
    }

    public static function findEntries($search, $group_id) {
      global $osC_Database;

      $result = array('entries' => array());

      $Qentries = $osC_Database->query('select SQL_CALC_FOUND_ROWS z2gz.*, c.countries_name, z.zone_name from :table_zones_to_geo_zones z2gz, :table_countries c, :table_zones z where z2gz.geo_zone_id = :geo_zone_id and z2gz.zone_country_id = c.countries_id and z2gz.zone_id = z.zone_id and (c.countries_name like :countries_name or z.zone_name like :zone_name) order by c.countries_name, z.zone_name');
      $Qentries->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
      $Qentries->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qentries->bindTable(':table_zones', TABLE_ZONES);
      $Qentries->bindInt(':geo_zone_id', $group_id);
      $Qentries->bindValue(':countries_name', '%' . $search . '%');
      $Qentries->bindValue(':zone_name', '%' . $search . '%');
      $Qentries->execute();

      while ( $Qentries->next() ) {
        $result['entries'][] = $Qentries->toArray();
      }

      $result['total'] = $Qentries->numberOfRows();

      $Qentries->freeResult();

      return $result;
    }

    public static function saveEntry($id = null, $data) {
      global $osC_Database;

      if ( is_numeric($id) ) {
        $Qentry = $osC_Database->query('update :table_zones_to_geo_zones set zone_country_id = :zone_country_id, zone_id = :zone_id, last_modified = now() where association_id = :association_id');
        $Qentry->bindInt(':association_id', $id);
      } else {
        $Qentry = $osC_Database->query('insert into :table_zones_to_geo_zones (zone_country_id, zone_id, geo_zone_id, date_added) values (:zone_country_id, :zone_id, :geo_zone_id, now())');
        $Qentry->bindInt(':geo_zone_id', $data['group_id']);
      }
      $Qentry->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
      $Qentry->bindInt(':zone_country_id', $data['country_id']);
      $Qentry->bindInt(':zone_id', $data['zone_id']);
      $Qentry->setLogging($_SESSION['module'], $id);
      $Qentry->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }

    public static function deleteEntry($id) {
      global $osC_Database;

      $Qentry = $osC_Database->query('delete from :table_zones_to_geo_zones where association_id = :association_id');
      $Qentry->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
      $Qentry->bindInt(':association_id', $id);
      $Qentry->setLogging($_SESSION['module'], $id);
      $Qentry->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }
  }
?>
