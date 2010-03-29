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

  class osC_Currencies_Admin {
    public static function get($id, $key = null) {
      global $osC_Database;

      $result = false;

      $Qcurrency = $osC_Database->query('select * from :table_currencies where');

      if ( is_numeric($id) ) {
        $Qcurrency->appendQuery('currencies_id = :currencies_id');
        $Qcurrency->bindInt(':currencies_id', $id);
      } else {
        $Qcurrency->appendQuery('code = :code');
        $Qcurrency->bindValue(':code', $id);
      }

      $Qcurrency->bindTable(':table_currencies', TABLE_CURRENCIES);
      $Qcurrency->execute();

      if ( $Qcurrency->numberOfRows() === 1 ) {
        $result = $Qcurrency->toArray();

        if ( !empty($key) && isset($result[$key]) ) {
          $result = $result[$key];
        }
      }

      return $result;
    }

    public static function exists($id) {
      return (self::get($id) !== false);
    }

    public static function getAll($pageset = 1) {
      global $osC_Database;

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qcurrencies = $osC_Database->query('select SQL_CALC_FOUND_ROWS * from :table_currencies order by title');
      $Qcurrencies->bindTable(':table_currencies', TABLE_CURRENCIES);

      if ( $pageset !== -1 ) {
        $Qcurrencies->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qcurrencies->execute();

      while ( $Qcurrencies->next() ) {
        $result['entries'][] = $Qcurrencies->toArray();
      }

      $result['total'] = $Qcurrencies->getBatchSize();

      $Qcurrencies->freeResult();

      return $result;
    }

    public static function find($search, $pageset = 1) {
      global $osC_Database;

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qcurrencies = $osC_Database->query('select SQL_CALC_FOUND_ROWS * from :table_currencies where (title like :title or code like :code or symbol_left like :symbol_left or symbol_right like :symbol_right) order by title');
      $Qcurrencies->bindTable(':table_currencies', TABLE_CURRENCIES);
      $Qcurrencies->bindValue(':title', '%' . $search . '%');
      $Qcurrencies->bindValue(':code', '%' . $search . '%');
      $Qcurrencies->bindValue(':symbol_left', '%' . $search . '%');
      $Qcurrencies->bindValue(':symbol_right', '%' . $search . '%');

      if ( $pageset !== -1 ) {
        $Qcurrencies->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qcurrencies->execute();

      while ( $Qcurrencies->next() ) {
        $result['entries'][] = $Qcurrencies->toArray();
      }

      $result['total'] = $Qcurrencies->getBatchSize();

      $Qcurrencies->freeResult();

      return $result;
    }

    public static function save($id = null, $data, $set_default = false) {
      global $osC_Database;

      $osC_Database->startTransaction();

      if ( is_numeric($id) ) {
        $Qcurrency = $osC_Database->query('update :table_currencies set title = :title, code = :code, symbol_left = :symbol_left, symbol_right = :symbol_right, decimal_places = :decimal_places, value = :value where currencies_id = :currencies_id');
        $Qcurrency->bindInt(':currencies_id', $id);
      } else {
        $Qcurrency = $osC_Database->query('insert into :table_currencies (title, code, symbol_left, symbol_right, decimal_places, value) values (:title, :code, :symbol_left, :symbol_right, :decimal_places, :value)');
      }

      $Qcurrency->bindTable(':table_currencies', TABLE_CURRENCIES);
      $Qcurrency->bindValue(':title', $data['title']);
      $Qcurrency->bindValue(':code', $data['code']);
      $Qcurrency->bindValue(':symbol_left', $data['symbol_left']);
      $Qcurrency->bindValue(':symbol_right', $data['symbol_right']);
      $Qcurrency->bindInt(':decimal_places', $data['decimal_places']);
      $Qcurrency->bindValue(':value', $data['value']);
      $Qcurrency->setLogging($_SESSION['module'], $id);
      $Qcurrency->execute();

      if ( $osC_Database->isError() === false ) {
        if ( is_numeric($id) === false ) {
          $id = $osC_Database->nextID();
        }

        if ( $set_default === true ) {
          $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
          $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qupdate->bindValue(':configuration_value', $data['code']);
          $Qupdate->bindValue(':configuration_key', 'DEFAULT_CURRENCY');
          $Qupdate->setLogging($_SESSION['module'], $id);
          $Qupdate->execute();
        }

        if ( $osC_Database->isError() === false ) {
          $osC_Database->commitTransaction();

          osC_Cache::clear('currencies');

          if ( ( $set_default === true ) && $Qupdate->affectedRows() ) {
            osC_Cache::clear('configuration');
          }

          return true;
        }
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    public static function delete($id) {
      global $osC_Database;

      $Qcheck = $osC_Database->query('select code from :table_currencies where currencies_id = :currencies_id');
      $Qcheck->bindTable(':table_currencies', TABLE_CURRENCIES);
      $Qcheck->bindInt(':currencies_id', $id);
      $Qcheck->execute();

      if ( $Qcheck->value('code') != DEFAULT_CURRENCY ) {
        $Qdelete = $osC_Database->query('delete from :table_currencies where currencies_id = :currencies_id');
        $Qdelete->bindTable(':table_currencies', TABLE_CURRENCIES);
        $Qdelete->bindInt(':currencies_id', $id);
        $Qdelete->setLogging($_SESSION['module'], $id);
        $Qdelete->execute();

        if ( $osC_Database->isError() === false ) {
          osC_Cache::clear('currencies');

          return true;
        }
      }

      return false;
    }

    public static function updateRates($service) {
      global $osC_Database;

      $updated = array('0' => array(),
                       '1' => array());

      foreach ( osc_toObjectInfo(self::getAll(-1))->get('entries') as $currency ) {
        $rate = call_user_func('quote_' . $service . '_currency', $currency['code']);

        if ( !empty($rate) ) {
          $Qupdate = $osC_Database->query('update :table_currencies set value = :value, last_updated = now() where currencies_id = :currencies_id');
          $Qupdate->bindTable(':table_currencies', TABLE_CURRENCIES);
          $Qupdate->bindValue(':value', $rate);
          $Qupdate->bindInt(':currencies_id', $currency['currencies_id']);
          $Qupdate->setLogging($_SESSION['module'], $currency['currencies_id']);
          $Qupdate->execute();

          $updated[1][] = array('title' => $currency['title'],
                                'code' => $currency['code']);
        } else {
          $updated[0][] = array('title' => $currency['title'],
                                'code' => $currency['code']);
        }
      }

      osC_Cache::clear('currencies');

      return $updated;
    }
  }
?>
