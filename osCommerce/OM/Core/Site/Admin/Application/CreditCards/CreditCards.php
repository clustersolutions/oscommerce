<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\CreditCards;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Cache;

  class CreditCards {
    public static function get($id) {
      $OSCOM_Database = Registry::get('Database');

      $Qcc = $OSCOM_Database->query('select * from :table_credit_cards where id = :id');
      $Qcc->bindInt(':id', $id);
      $Qcc->execute();

      return $Qcc->toArray();
    }

    public static function getAll($pageset = 1) {
      $OSCOM_Database = Registry::get('Database');

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array();

      $Qcc = $OSCOM_Database->query('select SQL_CALC_FOUND_ROWS * from :table_credit_cards order by sort_order, credit_card_name');

      if ( $pageset !== -1 ) {
        $Qcc->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qcc->execute();

      $result['entries'] = $Qcc->getAll();

      $result['total'] = $Qcc->getBatchSize();

      return $result;
    }

    public static function find($search, $pageset = 1) {
      $OSCOM_Database = Registry::get('Database');

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array();

      $Qcc = $OSCOM_Database->query('select SQL_CALC_FOUND_ROWS * from :table_credit_cards where (credit_card_name like :credit_card_name) order by credit_card_name');
      $Qcc->bindValue(':credit_card_name', '%' . $search . '%');

      if ( $pageset !== -1 ) {
        $Qcc->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qcc->execute();

      $result['entries'] = $Qcc->getAll();

      $result['total'] = $Qcc->getBatchSize();

      return $result;
    }

    public static function save($id = null, $data) {
      $OSCOM_Database = Registry::get('Database');

      if ( is_numeric($id) ) {
        $Qcc = $OSCOM_Database->query('update :table_credit_cards set credit_card_name = :credit_card_name, pattern = :pattern, credit_card_status = :credit_card_status, sort_order = :sort_order where id = :id');
        $Qcc->bindInt(':id', $id);
      } else {
        $Qcc = $OSCOM_Database->query('insert into :table_credit_cards (credit_card_name, pattern, credit_card_status, sort_order) values (:credit_card_name, :pattern, :credit_card_status, :sort_order)');
      }

      $Qcc->bindValue(':credit_card_name', $data['name']);
      $Qcc->bindValue(':pattern', $data['pattern']);
      $Qcc->bindInt(':credit_card_status', $data['status']);
      $Qcc->bindInt(':sort_order', $data['sort_order']);
      $Qcc->setLogging(null, $id);
      $Qcc->execute();

      if ( $Qcc->affectedRows() ) {
        Cache::clear('credit-cards');

        return true;
      }

      return false;
    }

    public static function delete($id) {
      $OSCOM_Database = Registry::get('Database');

      $Qdel = $OSCOM_Database->query('delete from :table_credit_cards where id = :id');
      $Qdel->bindInt(':id', $id);
      $Qdel->setLogging(null, $id);
      $Qdel->execute();

      if ( $Qdel->affectedRows() ) {
        Cache::clear('credit-cards');

        return true;
      }

      return false;
    }

    public static function setStatus($id, $status) {
      $OSCOM_Database = Registry::get('Database');

      $Qcc = $OSCOM_Database->query('update :table_credit_cards set credit_card_status = :credit_card_status where id = :id');
      $Qcc->bindInt(':credit_card_status', ($status === true) ? 1 : 0);
      $Qcc->bindInt(':id', $id);
      $Qcc->setLogging(null, $id);
      $Qcc->execute();

      if ( $Qcc->affectedRows() ) {
        Cache::clear('credit-cards');

        return true;
      }
    }
  }
?>
