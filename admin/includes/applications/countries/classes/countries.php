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

  class osC_Countries_Admin {
    public static function get($id) {
      global $osC_Database;

      $Qcountries = $osC_Database->query('select * from :table_countries where countries_id = :countries_id');
      $Qcountries->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qcountries->bindInt(':countries_id', $id);
      $Qcountries->execute();

      $Qzones = $osC_Database->query('select count(*) as total_zones from :table_zones where zone_country_id = :zone_country_id');
      $Qzones->bindTable(':table_zones', TABLE_ZONES);
      $Qzones->bindInt(':zone_country_id', $id);
      $Qzones->execute();

      $data = array_merge($Qcountries->toArray(), $Qzones->toArray());

      $Qzones->freeResult();
      $Qcountries->freeResult();

      return $data;
    }

    public static function getAll($pageset = 1) {
      global $osC_Database;

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qcountries = $osC_Database->query('select SQL_CALC_FOUND_ROWS * from :table_countries order by countries_name');
      $Qcountries->bindTable(':table_countries', TABLE_COUNTRIES);

      if ( $pageset !== -1 ) {
        $Qcountries->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qcountries->execute();

      while ( $Qcountries->next() ) {
        $Qzones = $osC_Database->query('select count(*) as total_zones from :table_zones where zone_country_id = :zone_country_id');
        $Qzones->bindTable(':table_zones', TABLE_ZONES);
        $Qzones->bindInt(':zone_country_id', $Qcountries->valueInt('countries_id'));
        $Qzones->execute();

        $result['entries'][] = array_merge($Qcountries->toArray(), $Qzones->toArray());
      }

      $result['total'] = $Qcountries->getBatchSize();

      if ( $Qcountries->numberOfRows() > 0 ) {
        $Qzones->freeResult();
      }

      $Qcountries->freeResult();

      return $result;
    }

    public static function find($search, $pageset = 1) {
      global $osC_Database;

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qcountries = $osC_Database->query('select SQL_CALC_FOUND_ROWS c.* from :table_countries c left join :table_zones z on (z.zone_country_id = c.countries_id) where (c.countries_name like :countries_name or c.countries_iso_code_2 like :countries_iso_code_2 or c.countries_iso_code_3 like :countries_iso_code_3 or z.zone_name like :zone_name or z.zone_code like :zone_code) group by c.countries_id order by c.countries_name');
      $Qcountries->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qcountries->bindTable(':table_zones', TABLE_ZONES);
      $Qcountries->bindValue(':countries_name', '%' . $search . '%');
      $Qcountries->bindValue(':countries_iso_code_2', '%' . $search . '%');
      $Qcountries->bindValue(':countries_iso_code_3', '%' . $search . '%');
      $Qcountries->bindValue(':zone_name', '%' . $search . '%');
      $Qcountries->bindValue(':zone_code', '%' . $search . '%');

      if ( $pageset !== -1 ) {
        $Qcountries->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qcountries->execute();

      while ( $Qcountries->next() ) {
        $Qzones = $osC_Database->query('select count(*) as total_zones from :table_zones where zone_country_id = :zone_country_id');
        $Qzones->bindTable(':table_zones', TABLE_ZONES);
        $Qzones->bindInt(':zone_country_id', $Qcountries->valueInt('countries_id'));
        $Qzones->execute();

        $result['entries'][] = array_merge($Qcountries->toArray(), $Qzones->toArray());
      }

      $result['total'] = $Qcountries->getBatchSize();

      if ( $Qcountries->numberOfRows() > 0 ) {
        $Qzones->freeResult();
      }

      $Qcountries->freeResult();

      return $result;
    }

    public static function findZones($search, $country_id) {
      global $osC_Database;

      $result = array('entries' => array());

      $Qzones = $osC_Database->query('select SQL_CALC_FOUND_ROWS * from :table_zones where zone_country_id = :zone_country_id and (zone_name like :zone_name or zone_code like :zone_code) order by zone_name');
      $Qzones->bindTable(':table_zones', TABLE_ZONES);
      $Qzones->bindInt(':zone_country_id', $country_id);
      $Qzones->bindValue(':zone_name', '%' . $search . '%');
      $Qzones->bindValue(':zone_code', '%' . $search . '%');
      $Qzones->execute();

      while ( $Qzones->next() ) {
        $result['entries'][] = $Qzones->toArray();
      }

      $result['total'] = $Qzones->numberOfRows();

      $Qzones->freeResult();

      return $result;
    }

    public static function getZone($id) {
      global $osC_Database;

      $Qzones = $osC_Database->query('select * from :table_zones where zone_id = :zone_id');
      $Qzones->bindTable(':table_zones', TABLE_ZONES);
      $Qzones->bindInt(':zone_id', $id);
      $Qzones->execute();

      $data = $Qzones->toArray();

      $Qzones->freeResult();

      return $data;
    }

    public static function getAllZones($country_id) {
      global $osC_Database;

      $result = array('entries' => array());

      $Qzones = $osC_Database->query('select * from :table_zones where zone_country_id = :zone_country_id');
      $Qzones->bindTable(':table_zones', TABLE_ZONES);
      $Qzones->bindInt(':zone_country_id', $country_id);
      $Qzones->execute();

      while ( $Qzones->next() ) {
        $result['entries'][] = $Qzones->toArray();
      }

      $result['total'] = $Qzones->numberOfRows();

      $Qzones->freeResult();

      return $result;
    }

    public static function save($id = null, $data) {
      global $osC_Database;

      if ( is_numeric($id) ) {
        $Qcountry = $osC_Database->query('update :table_countries set countries_name = :countries_name, countries_iso_code_2 = :countries_iso_code_2, countries_iso_code_3 = :countries_iso_code_3, address_format = :address_format where countries_id = :countries_id');
        $Qcountry->bindInt(':countries_id', $id);
      } else {
        $Qcountry = $osC_Database->query('insert into :table_countries (countries_name, countries_iso_code_2, countries_iso_code_3, address_format) values (:countries_name, :countries_iso_code_2, :countries_iso_code_3, :address_format)');
      }

      $Qcountry->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qcountry->bindValue(':countries_name', $data['name']);
      $Qcountry->bindValue(':countries_iso_code_2', $data['iso_code_2']);
      $Qcountry->bindValue(':countries_iso_code_3', $data['iso_code_3']);
      $Qcountry->bindValue(':address_format', $data['address_format']);
      $Qcountry->setLogging($_SESSION['module'], $id);
      $Qcountry->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }

    public static function delete($id) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      $Qzones = $osC_Database->query('delete from :table_zones where zone_country_id = :zone_country_id');
      $Qzones->bindTable(':table_zones', TABLE_ZONES);
      $Qzones->bindInt(':zone_country_id', $id);
      $Qzones->setLogging($_SESSION['module'], $id);
      $Qzones->execute();

      if ( !$osC_Database->isError() ) {
        $Qcountry = $osC_Database->query('delete from :table_countries where countries_id = :countries_id');
        $Qcountry->bindTable(':table_countries', TABLE_COUNTRIES);
        $Qcountry->bindInt(':countries_id', $id);
        $Qcountry->setLogging($_SESSION['module'], $id);
        $Qcountry->execute();

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

    public static function saveZone($id = null, $data) {
      global $osC_Database;

      if ( is_numeric($id) ) {
        $Qzone = $osC_Database->query('update :table_zones set zone_name = :zone_name, zone_code = :zone_code, zone_country_id = :zone_country_id where zone_id = :zone_id');
        $Qzone->bindInt(':zone_id', $id);
      } else {
        $Qzone = $osC_Database->query('insert into :table_zones (zone_name, zone_code, zone_country_id) values (:zone_name, :zone_code, :zone_country_id)');
      }
      $Qzone->bindTable(':table_zones', TABLE_ZONES);
      $Qzone->bindValue(':zone_name', $data['name']);
      $Qzone->bindValue(':zone_code', $data['code']);
      $Qzone->bindInt(':zone_country_id', $data['country_id']);
      $Qzone->setLogging($_SESSION['module'], $id);
      $Qzone->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }

    public static function deleteZone($id) {
      global $osC_Database;

      $Qzone = $osC_Database->query('delete from :table_zones where zone_id = :zone_id');
      $Qzone->bindTable(':table_zones', TABLE_ZONES);
      $Qzone->bindInt(':zone_id', $id);
      $Qzone->setLogging($_SESSION['module'], $id);
      $Qzone->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }
  }
?>
