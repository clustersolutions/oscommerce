<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries;

  use osCommerce\OM\Core\Registry;

  class Countries {
    public static function get($id, $key = null) {
      $OSCOM_Database = Registry::get('Database');

      $Qcountries = $OSCOM_Database->query('select * from :table_countries where countries_id = :countries_id');
      $Qcountries->bindInt(':countries_id', $id);
      $Qcountries->execute();

      $Qzones = $OSCOM_Database->query('select count(*) as total_zones from :table_zones where zone_country_id = :zone_country_id');
      $Qzones->bindInt(':zone_country_id', $id);
      $Qzones->execute();

      $data = array_merge($Qcountries->toArray(), $Qzones->toArray());

      if ( !empty($key) && isset($data[$key]) ) {
        $data = $data[$key];
      }

      return $data;
    }

    public static function getAll($pageset = 1) {
      $OSCOM_Database = Registry::get('Database');

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array();

      $Qcountries = $OSCOM_Database->query('select SQL_CALC_FOUND_ROWS c.*, count(z.zone_id) as total_zones from :table_countries c, :table_zones z where c.countries_id = z.zone_country_id group by c.countries_id order by c.countries_name');

      if ( $pageset !== -1 ) {
        $Qcountries->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qcountries->execute();

      $result['entries'] = $Qcountries->getAll();

      $result['total'] = $Qcountries->getBatchSize();

      return $result;
    }

    public static function find($search, $pageset = 1) {
      $OSCOM_Database = Registry::get('Database');

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array();

      $Qcountries = $OSCOM_Database->query('select SQL_CALC_FOUND_ROWS c.*, count(z.zone_id) as total_zones from :table_countries c, :table_zones z where (c.countries_name like :countries_name or c.countries_iso_code_2 like :countries_iso_code_2 or c.countries_iso_code_3 like :countries_iso_code_3 or z.zone_name like :zone_name or z.zone_code like :zone_code) and c.countries_id = z.zone_country_id group by c.countries_id order by c.countries_name');
      $Qcountries->bindValue(':countries_name', '%' . $search . '%');
      $Qcountries->bindValue(':countries_iso_code_2', '%' . $search . '%');
      $Qcountries->bindValue(':countries_iso_code_3', '%' . $search . '%');
      $Qcountries->bindValue(':zone_name', '%' . $search . '%');
      $Qcountries->bindValue(':zone_code', '%' . $search . '%');

      if ( $pageset !== -1 ) {
        $Qcountries->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qcountries->execute();

      $result['entries'] = $Qcountries->getAll();

      $result['total'] = $Qcountries->getBatchSize();

      return $result;
    }

    public static function findZones($search, $country_id) {
      $OSCOM_Database = Registry::get('Database');

      $result = array();

      $Qzones = $OSCOM_Database->query('select SQL_CALC_FOUND_ROWS * from :table_zones where zone_country_id = :zone_country_id and (zone_name like :zone_name or zone_code like :zone_code) order by zone_name');
      $Qzones->bindInt(':zone_country_id', $country_id);
      $Qzones->bindValue(':zone_name', '%' . $search . '%');
      $Qzones->bindValue(':zone_code', '%' . $search . '%');
      $Qzones->execute();

      $result['entries'] = $Qzones->getAll();

      $result['total'] = $Qzones->numberOfRows();

      return $result;
    }

    public static function getZone($id) {
      $OSCOM_Database = Registry::get('Database');

      $Qzones = $OSCOM_Database->query('select * from :table_zones where zone_id = :zone_id');
      $Qzones->bindInt(':zone_id', $id);
      $Qzones->execute();

      return $Qzones->toArray();
    }

    public static function getAllZones($country_id) {
      $OSCOM_Database = Registry::get('Database');

      $result = array();

      $Qzones = $OSCOM_Database->query('select * from :table_zones where zone_country_id = :zone_country_id order by zone_name');
      $Qzones->bindInt(':zone_country_id', $country_id);
      $Qzones->execute();

      $result['entries'] = $Qzones->getAll();

      $result['total'] = $Qzones->numberOfRows();

      return $result;
    }

    public static function save($id = null, $data) {
      $OSCOM_Database = Registry::get('Database');

      if ( is_numeric($id) ) {
        $Qcountry = $OSCOM_Database->query('update :table_countries set countries_name = :countries_name, countries_iso_code_2 = :countries_iso_code_2, countries_iso_code_3 = :countries_iso_code_3, address_format = :address_format where countries_id = :countries_id');
        $Qcountry->bindInt(':countries_id', $id, false);
      } else {
        $Qcountry = $OSCOM_Database->query('insert into :table_countries (countries_name, countries_iso_code_2, countries_iso_code_3, address_format) values (:countries_name, :countries_iso_code_2, :countries_iso_code_3, :address_format)');
      }

      $Qcountry->bindValue(':countries_name', $data['name']);
      $Qcountry->bindValue(':countries_iso_code_2', $data['iso_code_2']);
      $Qcountry->bindValue(':countries_iso_code_3', $data['iso_code_3']);
      $Qcountry->bindValue(':address_format', $data['address_format']);
      $Qcountry->setLogging(null, $id);
      $Qcountry->execute();

      return !$OSCOM_Database->isError();
    }

    public static function delete($id) {
      $OSCOM_Database = Registry::get('Database');

      $Qcountry = $OSCOM_Database->query('delete from :table_countries where countries_id = :countries_id');
      $Qcountry->bindInt(':countries_id', $id, false);
      $Qcountry->setLogging(null, $id);
      $Qcountry->execute();

      return !$OSCOM_Database->isError();
    }

    public static function saveZone($id = null, $data) {
      $OSCOM_Database = Registry::get('Database');

      if ( is_numeric($id) ) {
        $Qzone = $OSCOM_Database->query('update :table_zones set zone_name = :zone_name, zone_code = :zone_code, zone_country_id = :zone_country_id where zone_id = :zone_id');
        $Qzone->bindInt(':zone_id', $id, false);
      } else {
        $Qzone = $OSCOM_Database->query('insert into :table_zones (zone_name, zone_code, zone_country_id) values (:zone_name, :zone_code, :zone_country_id)');
      }
      $Qzone->bindValue(':zone_name', $data['name']);
      $Qzone->bindValue(':zone_code', $data['code']);
      $Qzone->bindInt(':zone_country_id', $data['country_id']);
      $Qzone->setLogging(null, $id);
      $Qzone->execute();

      return !$OSCOM_Database->isError();
    }

    public static function deleteZone($id) {
      $OSCOM_Database = Registry::get('Database');

      $Qzone = $OSCOM_Database->query('delete from :table_zones where zone_id = :zone_id');
      $Qzone->bindInt(':zone_id', $id, false);
      $Qzone->setLogging(null, $id);
      $Qzone->execute();

      return !$OSCOM_Database->isError();
    }
  }
?>
