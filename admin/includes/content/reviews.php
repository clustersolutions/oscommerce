<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Reviews extends osC_Template {

/* Private variables */

    var $_module = 'reviews',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'reviews.php';

/* Class constructor */

    function osC_Content_Reviews() {
      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      if (!empty($_GET['action'])) {
        if ( ($_GET['action'] == 'update') && isset($_POST['review_edit']) ) {
          $_GET['action'] = 'rEdit';
        }

        switch ($_GET['action']) {
          case 'rEdit':
            $this->_page_contents = 'reviews_edit.php';
            break;

          case 'rPreview':
            $this->_page_contents = 'reviews_preview.php';
            break;

          case 'update':
            $this->_update();
            break;

          case 'deleteconfirm':
            $this->_delete();
            break;

          case 'rApprove':
            $this->_approve();
            break;

          case 'rReject':
            $this->_reject();
            break;
        }
      }
    }

/* Private methods */

    function _update() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['rID']) && is_numeric($_GET['rID'])) {
        $Qreview = $osC_Database->query('update :table_reviews set reviews_text = :reviews_text, reviews_rating = :reviews_rating, last_modified = now() where reviews_id = :reviews_id');
        $Qreview->bindTable(':table_reviews', TABLE_REVIEWS);
        $Qreview->bindValue(':reviews_text', $_POST['reviews_text']);
        $Qreview->bindInt(':reviews_rating', $_POST['reviews_rating']);
        $Qreview->bindInt(':reviews_id', $_GET['rID']);
        $Qreview->execute();

        if (!$osC_Database->isError()) {
          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . '&rID=' . $_GET['rID']));
      }
    }

    function _delete() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['rID']) && is_numeric($_GET['rID'])) {
        $Qreview = $osC_Database->query('delete from :table_reviews where reviews_id = :reviews_id');
        $Qreview->bindTable(':table_reviews', TABLE_REVIEWS);
        $Qreview->bindInt(':reviews_id', $_GET['rID']);
        $Qreview->execute();

        if (!$osC_Database->isError()) {
          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
      }
    }

    function _approve() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['rID']) && is_numeric($_GET['rID'])) {
        $Qreview = $osC_Database->query('update :table_reviews set reviews_status = 1 where reviews_id = :reviews_id');
        $Qreview->bindTable(':table_reviews', TABLE_REVIEWS);
        $Qreview->bindInt(':reviews_id', $_GET['rID']);
        $Qreview->execute();

        if (!$osC_Database->isError()) {
          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
      }
    }

    function _reject() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['rID']) && is_numeric($_GET['rID'])) {
        $Qreview = $osC_Database->query('update :table_reviews set reviews_status = 2 where reviews_id = :reviews_id');
        $Qreview->bindTable(':table_reviews', TABLE_REVIEWS);
        $Qreview->bindInt(':reviews_id', $_GET['rID']);
        $Qreview->execute();

        if (!$osC_Database->isError()) {
          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
      }
    }
  }
?>
