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

  class osC_TaxClasses_Admin {
    public static function get($id, $key = null) {
      global $osC_Database;

      $Qclasses = $osC_Database->query('select * from :table_tax_class where tax_class_id = :tax_class_id');
      $Qclasses->bindTable(':table_tax_class', TABLE_TAX_CLASS);
      $Qclasses->bindInt(':tax_class_id', $id);
      $Qclasses->execute();

      $Qrates = $osC_Database->query('select count(*) as total_tax_rates from :table_tax_rates where tax_class_id = :tax_class_id');
      $Qrates->bindTable(':table_tax_rates', TABLE_TAX_RATES);
      $Qrates->bindInt(':tax_class_id', $id);
      $Qrates->execute();

      $data = array_merge($Qclasses->toArray(), $Qrates->toArray());

      $Qrates->freeResult();
      $Qclasses->freeResult();

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

      $Qclasses = $osC_Database->query('select SQL_CALC_FOUND_ROWS * from :table_tax_class order by tax_class_title');
      $Qclasses->bindTable(':table_tax_class', TABLE_TAX_CLASS);

      if ( $pageset !== -1 ) {
        $Qclasses->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qclasses->execute();

      while ( $Qclasses->next() ) {
        $Qrates = $osC_Database->query('select count(*) as total_tax_rates from :table_tax_rates where tax_class_id = :tax_class_id');
        $Qrates->bindTable(':table_tax_rates', TABLE_TAX_RATES);
        $Qrates->bindInt(':tax_class_id', $Qclasses->valueInt('tax_class_id'));
        $Qrates->execute();

        $result['entries'][] = array_merge($Qclasses->toArray(), $Qrates->toArray());
      }

      $result['total'] = $Qclasses->getBatchSize();

      $Qclasses->freeResult();

      return $result;
    }

    public static function getEntryData($id) {
      global $osC_Database;

      $Qrates = $osC_Database->query('select r.*, tc.tax_class_title, z.geo_zone_id, z.geo_zone_name from :table_tax_rates r, :table_tax_class tc, :table_geo_zones z where r.tax_rates_id = :tax_rates_id and r.tax_class_id = tc.tax_class_id and r.tax_zone_id = z.geo_zone_id');
      $Qrates->bindTable(':table_tax_rates', TABLE_TAX_RATES);
      $Qrates->bindTable(':table_tax_class', TABLE_TAX_CLASS);
      $Qrates->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
      $Qrates->bindInt(':tax_rates_id', $id);
      $Qrates->execute();

      $data = $Qrates->toArray();

      $Qrates->freeResult();

      return $data;
    }

    public static function save($id = null, $data) {
      global $osC_Database;

      if ( is_numeric($id) ) {
        $Qclass = $osC_Database->query('update :table_tax_class set tax_class_title = :tax_class_title, tax_class_description = :tax_class_description, last_modified = now() where tax_class_id = :tax_class_id');
        $Qclass->bindInt(':tax_class_id', $id);
      } else {
        $Qclass = $osC_Database->query('insert into :table_tax_class (tax_class_title, tax_class_description, date_added) values (:tax_class_title, :tax_class_description, now())');
      }

      $Qclass->bindTable(':table_tax_class', TABLE_TAX_CLASS);
      $Qclass->bindValue(':tax_class_title', $data['title']);
      $Qclass->bindValue(':tax_class_description', $data['description']);
      $Qclass->setLogging($_SESSION['module'], $id);
      $Qclass->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }

    public static function delete($id) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      $Qrates = $osC_Database->query('delete from :table_tax_rates where tax_class_id = :tax_class_id');
      $Qrates->bindTable(':table_tax_rates', TABLE_TAX_RATES);
      $Qrates->bindInt(':tax_class_id', $id);
      $Qrates->setLogging($_SESSION['module'], $id);
      $Qrates->execute();

      if ( !$osC_Database->isError() ) {
        $Qclass = $osC_Database->query('delete from :table_tax_class where tax_class_id = :tax_class_id');
        $Qclass->bindTable(':table_tax_class', TABLE_TAX_CLASS);
        $Qclass->bindInt(':tax_class_id', $id);
        $Qclass->setLogging($_SESSION['module'], $id);
        $Qclass->execute();

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

    public static function saveEntry($id = null, $data) {
      global $osC_Database;

      if ( is_numeric($id) ) {
        $Qrate = $osC_Database->query('update :table_tax_rates set tax_zone_id = :tax_zone_id, tax_priority = :tax_priority, tax_rate = :tax_rate, tax_description = :tax_description, last_modified = now() where tax_rates_id = :tax_rates_id');
        $Qrate->bindInt(':tax_rates_id', $id);
      } else {
        $Qrate = $osC_Database->query('insert into :table_tax_rates (tax_zone_id, tax_class_id, tax_priority, tax_rate, tax_description, date_added) values (:tax_zone_id, :tax_class_id, :tax_priority, :tax_rate, :tax_description, now())');
        $Qrate->bindInt(':tax_class_id', $data['tax_class_id']);
      }

      $Qrate->bindTable(':table_tax_rates', TABLE_TAX_RATES);
      $Qrate->bindInt(':tax_zone_id', $data['zone_id']);
      $Qrate->bindInt(':tax_priority', $data['priority']);
      $Qrate->bindValue(':tax_rate', $data['rate']);
      $Qrate->bindValue(':tax_description', $data['description']);
      $Qrate->setLogging($_SESSION['module'], $id);
      $Qrate->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }

    public static function deleteEntry($id) {
      global $osC_Database;

      $Qrate = $osC_Database->query('delete from :table_tax_rates where tax_rates_id = :tax_rates_id');
      $Qrate->bindTable(':table_tax_rates', TABLE_TAX_RATES);
      $Qrate->bindInt(':tax_rates_id', $id);
      $Qrate->setLogging($_SESSION['module'], $id);
      $Qrate->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }

    function hasProducts($id) {
      global $osC_Database;

      $Qcheck = $osC_Database->query('select products_id from :table_products where products_tax_class_id = :products_tax_class_id limit 1');
      $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
      $Qcheck->bindInt(':products_tax_class_id', $id);
      $Qcheck->execute();

      return ( $Qcheck->numberOfRows() === 1 );
    }

    function getNumberOFProducts($id) {
      global $osC_Database;

      $Qtotal = $osC_Database->query('select count(*) as total from :table_products where products_tax_class_id = :products_tax_class_id');
      $Qtotal->bindTable(':table_products', TABLE_PRODUCTS);
      $Qtotal->bindInt(':products_tax_class_id', $id);
      $Qtotal->execute();

      return $Qtotal->valueInt('total');
    }
  }
?>
