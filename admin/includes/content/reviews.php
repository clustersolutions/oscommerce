<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/reviews.php');

  class osC_Content_Reviews extends osC_Template {

/* Private variables */

    var $_module = 'reviews',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Reviews() {
      global $osC_MessageStack;

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      if ( !isset($_GET['page']) || ( isset($_GET['page']) && !is_numeric($_GET['page']) ) ) {
        $_GET['page'] = 1;
      }

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'preview':
            $this->_page_contents = 'preview.php';

            break;

          case 'save':
            $this->_page_contents = 'edit.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('review' => $_POST['reviews_text'],
                            'rating' => $_POST['reviews_rating']);

              if ( osC_Reviews_Admin::save($_GET['rID'], $data) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
              }

              osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;

          case 'delete':
            $this->_page_contents = 'delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Reviews_Admin::delete($_GET['rID']) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
              }

              osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;

          case 'batchDelete':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              $this->_page_contents = 'batch_delete.php';

              if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
                $error = false;

                foreach ($_POST['batch'] as $id) {
                  if ( !osC_Reviews_Admin::delete($id) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
                } else {
                  $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
                }

                osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
              }
            }

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
