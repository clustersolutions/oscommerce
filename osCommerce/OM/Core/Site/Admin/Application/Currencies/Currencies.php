<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Currencies;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Cache;

  class Currencies {
    public static function get($id, $key = null) {
      $OSCOM_Database = Registry::get('Database');

      $result = false;

      $Qcurrency = $OSCOM_Database->query('select * from :table_currencies where');

      if ( is_numeric($id) ) {
        $Qcurrency->appendQuery('currencies_id = :currencies_id');
        $Qcurrency->bindInt(':currencies_id', $id);
      } else {
        $Qcurrency->appendQuery('code = :code');
        $Qcurrency->bindValue(':code', $id);
      }

      $Qcurrency->appendQuery('limit 1');
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
      $OSCOM_Database = Registry::get('Database');

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array();

      $Qcurrencies = $OSCOM_Database->query('select SQL_CALC_FOUND_ROWS * from :table_currencies order by title');

      if ( $pageset !== -1 ) {
        $Qcurrencies->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qcurrencies->execute();

      $result['entries'] = $Qcurrencies->getAll();

      $result['total'] = $Qcurrencies->getBatchSize();

      return $result;
    }

    public static function find($search, $pageset = 1) {
      $OSCOM_Database = Registry::get('Database');

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array();

      $Qcurrencies = $OSCOM_Database->query('select SQL_CALC_FOUND_ROWS * from :table_currencies where (title like :title or code like :code or symbol_left like :symbol_left or symbol_right like :symbol_right) order by title');
      $Qcurrencies->bindValue(':title', '%' . $search . '%');
      $Qcurrencies->bindValue(':code', '%' . $search . '%');
      $Qcurrencies->bindValue(':symbol_left', '%' . $search . '%');
      $Qcurrencies->bindValue(':symbol_right', '%' . $search . '%');

      if ( $pageset !== -1 ) {
        $Qcurrencies->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qcurrencies->execute();

      $result['entries'] = $Qcurrencies->getAll();

      $result['total'] = $Qcurrencies->getBatchSize();

      return $result;
    }

    public static function save($id = null, $data, $set_default = false) {
      $OSCOM_Database = Registry::get('Database');

      $OSCOM_Database->startTransaction();

      if ( is_numeric($id) ) {
        $Qcurrency = $OSCOM_Database->query('update :table_currencies set title = :title, code = :code, symbol_left = :symbol_left, symbol_right = :symbol_right, decimal_places = :decimal_places, value = :value where currencies_id = :currencies_id');
        $Qcurrency->bindInt(':currencies_id', $id);
      } else {
        $Qcurrency = $OSCOM_Database->query('insert into :table_currencies (title, code, symbol_left, symbol_right, decimal_places, value) values (:title, :code, :symbol_left, :symbol_right, :decimal_places, :value)');
      }

      $Qcurrency->bindValue(':title', $data['title']);
      $Qcurrency->bindValue(':code', $data['code']);
      $Qcurrency->bindValue(':symbol_left', $data['symbol_left']);
      $Qcurrency->bindValue(':symbol_right', $data['symbol_right']);
      $Qcurrency->bindInt(':decimal_places', $data['decimal_places']);
      $Qcurrency->bindValue(':value', $data['value']);
      $Qcurrency->setLogging(null, $id);
      $Qcurrency->execute();

      if ( $OSCOM_Database->isError() === false ) {
        if ( is_numeric($id) === false ) {
          $id = $OSCOM_Database->nextID();
        }

        if ( $set_default === true ) {
          $Qupdate = $OSCOM_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
          $Qupdate->bindValue(':configuration_value', $data['code']);
          $Qupdate->bindValue(':configuration_key', 'DEFAULT_CURRENCY');
          $Qupdate->setLogging(null, $id);
          $Qupdate->execute();
        }

        if ( $OSCOM_Database->isError() === false ) {
          $OSCOM_Database->commitTransaction();

          Cache::clear('currencies');

          if ( ( $set_default === true ) && $Qupdate->affectedRows() ) {
            Cache::clear('configuration');
          }

          return true;
        }
      }

      $OSCOM_Database->rollbackTransaction();

      return false;
    }

    public static function delete($id) {
      $OSCOM_Database = Registry::get('Database');

      $Qcheck = $OSCOM_Database->query('select code from :table_currencies where currencies_id = :currencies_id');
      $Qcheck->bindInt(':currencies_id', $id);
      $Qcheck->execute();

      if ( $Qcheck->value('code') != DEFAULT_CURRENCY ) {
        $Qdelete = $OSCOM_Database->query('delete from :table_currencies where currencies_id = :currencies_id');
        $Qdelete->bindInt(':currencies_id', $id);
        $Qdelete->setLogging(null, $id);
        $Qdelete->execute();

        if ( $OSCOM_Database->isError() === false ) {
          Cache::clear('currencies');

          return true;
        }
      }

      return false;
    }

    public static function updateRates($service) {
      $OSCOM_Database = Registry::get('Database');

      $updated = array('0' => array(),
                       '1' => array());

      foreach ( osc_toObjectInfo(self::getAll(-1))->get('entries') as $currency ) {
        $rate = call_user_func('quote_' . $service . '_currency', $currency['code']);

        if ( !empty($rate) ) {
          $Qupdate = $OSCOM_Database->query('update :table_currencies set value = :value, last_updated = now() where currencies_id = :currencies_id');
          $Qupdate->bindValue(':value', $rate);
          $Qupdate->bindInt(':currencies_id', $currency['currencies_id']);
          $Qupdate->setLogging(null, $currency['currencies_id']);
          $Qupdate->execute();

          $updated[1][] = array('title' => $currency['title'],
                                'code' => $currency['code']);
        } else {
          $updated[0][] = array('title' => $currency['title'],
                                'code' => $currency['code']);
        }
      }

      Cache::clear('currencies');

      return $updated;
    }
  }
?>
