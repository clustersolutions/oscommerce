<?php
/*
  $Id: reviews.php,v 1.46 2004/11/01 09:43:11 sparky Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $selected_box = 'catalog';

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  if (!empty($action)) {
    switch ($action) {
      case 'update':
        if (isset($_POST['review_edit'])) {
          $action = 'rEdit';
        } else {
          if (isset($_GET['rID']) && is_numeric($_GET['rID'])) {
            $error = false;

            $osC_Database->startTransaction();

            $Qreview = $osC_Database->query('update :table_reviews set reviews_rating = :reviews_rating, last_modified = now() where reviews_id = :reviews_id');
            $Qreview->bindTable(':table_reviews', TABLE_REVIEWS);
            $Qreview->bindInt(':reviews_rating', $_POST['reviews_rating']);
            $Qreview->bindInt(':reviews_id', $_GET['rID']);
            $Qreview->execute();

            if ($osC_Database->isError() === false) {
              $Qrd = $osC_Database->query('update :table_reviews set reviews_text = :reviews_text where reviews_id = :reviews_id');
              $Qrd->bindTable(':table_reviews', TABLE_REVIEWS);
              $Qrd->bindValue(':reviews_text', $_POST['reviews_text']);
              $Qrd->bindInt(':reviews_id', $_GET['rID']);
              $Qrd->execute();

              if ($osC_Database->isError()) {
                $error = true;
              }
            } else {
              $error = true;
            }

            if ($error === false) {
              $osC_Database->commitTransaction();

              $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
            } else {
              $osC_Database->rollbackTransaction();

              $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
            }

            tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID']));
          }
        }
        break;
      case 'deleteconfirm':
        if (isset($_GET['rID']) && is_numeric($_GET['rID'])) {
          $error = false;

          $osC_Database->startTransaction();

          $Qreview = $osC_Database->query('delete from :table_reviews where reviews_id = :reviews_id');
          $Qreview->bindTable(':table_reviews', TABLE_REVIEWS);
          $Qreview->bindInt(':reviews_id', $_GET['rID']);
          $Qreview->execute();

          if ($osC_Database->isError() === false) {
            $osC_Database->commitTransaction();

            $messageStack->add_session(SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_Database->rollbackTransaction();

            $messageStack->add_session(ERROR_DB_ROWS_NOT_UPDATED, 'error');
          }

          tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page']));
        }
        break;
      case 'rApprove':
        if (isset($_GET['rID']) && is_numeric($_GET['rID'])) {
          $error = false;

          $osC_Database->startTransaction();

          $Qreview = $osC_Database->query('update :table_reviews set reviews_status = 1 where reviews_id = :reviews_id');
          $Qreview->bindTable(':table_reviews', TABLE_REVIEWS);
          $Qreview->bindInt(':reviews_id', $_GET['rID']);
          $Qreview->execute();

          if ($osC_Database->isError() === false) {
            $osC_Database->commitTransaction();

            $messageStack->add_session(SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_Database->rollbackTransaction();

            $messageStack->add_session(ERROR_DB_ROWS_NOT_UPDATED, 'error');
          }

          tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page']));
        }
        break;
      case 'rReject':
        if (isset($_GET['rID']) && is_numeric($_GET['rID'])) {
          $error = false;

          $osC_Database->startTransaction();

          $Qreview = $osC_Database->query('update :table_reviews set reviews_status = 2 where reviews_id = :reviews_id');
          $Qreview->bindTable(':table_reviews', TABLE_REVIEWS);
          $Qreview->bindInt(':reviews_id', $_GET['rID']);
          $Qreview->execute();

          if ($osC_Database->isError() === false) {
            $osC_Database->commitTransaction();

            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_Database->rollbackTransaction();

            $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
          }

          tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page']));
        }
        break;
    }
  }

  switch ($action) {
    case 'rEdit': $page_contents = 'reviews_edit.php'; break;
    case 'rPreview': $page_contents = 'reviews_preview.php'; break;
    default: $page_contents = 'reviews.php';
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
