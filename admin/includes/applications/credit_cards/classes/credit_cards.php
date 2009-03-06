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

  class osC_CreditCards_Admin {
    public static function get($id) {
      global $osC_Database;

      $Qcc = $osC_Database->query('select * from :table_credit_cards where id = :id');
      $Qcc->bindTable(':table_credit_cards', TABLE_CREDIT_CARDS);
      $Qcc->bindInt(':id', $id);
      $Qcc->execute();

      $result = $Qcc->toArray();

      $Qcc->freeResult();

      return $result;
    }

    public static function getAll($pageset = 1) {
      global $osC_Database;

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qcc = $osC_Database->query('select SQL_CALC_FOUND_ROWS * from :table_credit_cards order by sort_order, credit_card_name');
      $Qcc->bindTable(':table_credit_cards', TABLE_CREDIT_CARDS);

      if ( $pageset !== -1 ) {
        $Qcc->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qcc->execute();

      while ( $Qcc->next() ) {
        $result['entries'][] = $Qcc->toArray();
      }

      $result['total'] = $Qcc->getBatchSize();

      $Qcc->freeResult();

      return $result;
    }

    public static function find($search, $pageset = 1) {
      global $osC_Database;

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qcc = $osC_Database->query('select SQL_CALC_FOUND_ROWS * from :table_credit_cards where (credit_card_name like :credit_card_name) order by credit_card_name');
      $Qcc->bindTable(':table_credit_cards', TABLE_CREDIT_CARDS);
      $Qcc->bindValue(':credit_card_name', '%' . $search . '%');

      if ( $pageset !== -1 ) {
        $Qcc->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qcc->execute();

      while ( $Qcc->next() ) {
        $result['entries'][] = $Qcc->toArray();
      }

      $result['total'] = $Qcc->getBatchSize();

      $Qcc->freeResult();

      return $result;
    }

    public static function save($id = null, $data) {
      global $osC_Database;

      if ( is_numeric($id) ) {
        $Qcc = $osC_Database->query('update :table_credit_cards set credit_card_name = :credit_card_name, pattern = :pattern, credit_card_status = :credit_card_status, sort_order = :sort_order where id = :id');
        $Qcc->bindInt(':id', $id);
      } else {
        $Qcc = $osC_Database->query('insert into :table_credit_cards (credit_card_name, pattern, credit_card_status, sort_order) values (:credit_card_name, :pattern, :credit_card_status, :sort_order)');
      }

      $Qcc->bindTable(':table_credit_cards', TABLE_CREDIT_CARDS);
      $Qcc->bindValue(':credit_card_name', $data['name']);
      $Qcc->bindValue(':pattern', $data['pattern']);
      $Qcc->bindInt(':credit_card_status', $data['status']);
      $Qcc->bindInt(':sort_order', $data['sort_order']);
      $Qcc->setLogging($_SESSION['module'], $id);
      $Qcc->execute();

      if ( $Qcc->affectedRows() ) {
        osC_Cache::clear('credit-cards');

        return true;
      }

      return false;
    }

    public static function delete($id) {
      global $osC_Database;

      $Qdel = $osC_Database->query('delete from :table_credit_cards where id = :id');
      $Qdel->bindTable(':table_credit_cards', TABLE_CREDIT_CARDS);
      $Qdel->bindInt(':id', $id);
      $Qdel->setLogging($_SESSION['module'], $id);
      $Qdel->execute();

      if ( $Qdel->affectedRows() ) {
        osC_Cache::clear('credit-cards');

        return true;
      }

      return false;
    }

    public static function setStatus($id, $status) {
      global $osC_Database;

      $Qcc = $osC_Database->query('update :table_credit_cards set credit_card_status = :credit_card_status where id = :id');
      $Qcc->bindTable(':table_credit_cards', TABLE_CREDIT_CARDS);
      $Qcc->bindInt(':credit_card_status', ($status === true) ? 1 : 0);
      $Qcc->bindInt(':id', $id);
      $Qcc->setLogging($_SESSION['module'], $id);
      $Qcc->execute();

      if ( $Qcc->affectedRows() ) {
        osC_Cache::clear('credit-cards');

        return true;
      }
    }
  }
?>
