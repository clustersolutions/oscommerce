<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_CreditCards_Admin {
    function getData($id) {
      global $osC_Database;

      $Qcc = $osC_Database->query('select * from :table_credit_cards where id = :id');
      $Qcc->bindTable(':table_credit_cards', TABLE_CREDIT_CARDS);
      $Qcc->bindInt(':id', $id);
      $Qcc->execute();

      $result = $Qcc->toArray();

      $Qcc->freeResult();

      return $result;
    }

    function save($id = null, $data) {
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
      $Qcc->execute();

      if ( $Qcc->affectedRows() ) {
        osC_Cache::clear('credit-cards');

        return true;
      }

      return false;
    }

    function delete($id) {
      global $osC_Database;

      $Qdel = $osC_Database->query('delete from :table_credit_cards where id = :id');
      $Qdel->bindTable(':table_credit_cards', TABLE_CREDIT_CARDS);
      $Qdel->bindInt(':id', $id);
      $Qdel->execute();

      if ( $Qdel->affectedRows() ) {
        osC_Cache::clear('credit-cards');

        return true;
      }

      return false;
    }

    function setStatus($id, $status) {
      global $osC_Database;

      $Qcc = $osC_Database->query('update :table_credit_cards set credit_card_status = :credit_card_status where id = :id');
      $Qcc->bindTable(':table_credit_cards', TABLE_CREDIT_CARDS);
      $Qcc->bindInt(':credit_card_status', ($status === true) ? 1 : 0);
      $Qcc->bindInt(':id', $id);
      $Qcc->execute();

      if ( $Qcc->affectedRows() ) {
        osC_Cache::clear('credit-cards');

        return true;
      }
    }
  }
?>
