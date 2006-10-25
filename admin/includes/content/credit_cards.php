<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Credit_cards extends osC_Template {

/* Private variables */

    var $_module = 'credit_cards',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'credit_cards.php';

/* Class constructor */

    function osC_Content_Credit_cards() {
      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'save':
            $this->_save();
            break;

          case 'deleteconfirm':
            $this->_delete();
            break;
        }
      }
    }

/* Private methods */

    function _save() {
      global $osC_Database, $osC_MessageStack;

      $error = false;

      if (empty($_POST['credit_card_name'])) {
        $osC_MessageStack->add($this->_module, ERROR_CREDIT_CARD_NAME, 'error');
        $error = true;
      }

      if ($error === false) {
        if (isset($_GET['ccID']) && is_numeric($_GET['ccID'])) {
          $Qcc = $osC_Database->query('update :table_credit_cards set credit_card_name = :credit_card_name, pattern = :pattern, credit_card_status = :credit_card_status, sort_order = :sort_order where id = :id');
          $Qcc->bindInt(':id', $_GET['ccID']);
        } else {
          $Qcc = $osC_Database->query('insert into :table_credit_cards (credit_card_name, pattern, credit_card_status, sort_order) values (:credit_card_name, :pattern, :credit_card_status, :sort_order)');
        }
        $Qcc->bindTable(':table_credit_cards', TABLE_CREDIT_CARDS);
        $Qcc->bindValue(':credit_card_name', $_POST['credit_card_name']);
        $Qcc->bindValue(':pattern', $_POST['pattern']);
        $Qcc->bindInt(':credit_card_status', (isset($_POST['credit_card_status']) && ($_POST['credit_card_status'] == '1') ? '1' : '0'));
        $Qcc->bindInt(':sort_order', $_POST['sort_order']);
        $Qcc->execute();

        if ($Qcc->affectedRows()) {
          osC_Cache::clear('credit-cards');

          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
        }

        osc_redirect(osc_href_link(FILENAME_DEFAULT, $this->_module . '&ccID=' . ((isset($_GET['ccID']) && is_numeric($_GET['ccID'])) ? $_GET['ccID'] : $osC_Database->nextID())));
      }
    }

    function _delete() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['ccID']) && is_numeric($_GET['ccID'])) {
        $Qdel = $osC_Database->query('delete from :table_credit_cards where id = :id');
        $Qdel->bindTable(':table_credit_cards', TABLE_CREDIT_CARDS);
        $Qdel->bindInt(':id', $_GET['ccID']);
        $Qdel->execute();

        if ($Qdel->affectedRows()) {
          osC_Cache::clear('credit-cards');

          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
        }
      }

      osc_redirect(osc_href_link(FILENAME_DEFAULT, $this->_module));
    }
  }
?>
