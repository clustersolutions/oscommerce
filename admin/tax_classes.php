<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  $entriesAction = (isset($_GET['entriesAction']) ? $_GET['entriesAction'] : '');

  if (!isset($_GET['entriesPage']) || (isset($_GET['entriesPage']) && !is_numeric($_GET['entriesPage']))) {
    $_GET['entriesPage'] = 1;
  }

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        if (isset($_GET['tcID']) && is_numeric($_GET['tcID'])) {
          $Qclass = $osC_Database->query('update :table_tax_class set tax_class_title = :tax_class_title, tax_class_description = :tax_class_description, last_modified = now() where tax_class_id = :tax_class_id');
          $Qclass->bindInt(':tax_class_id', $_GET['tcID']);
        } else {
          $Qclass = $osC_Database->query('insert into :table_tax_class (tax_class_title, tax_class_description, date_added) values (:tax_class_title, :tax_class_description, now())');
        }
        $Qclass->bindTable(':table_tax_class', TABLE_TAX_CLASS);
        $Qclass->bindValue(':tax_class_title', $_POST['tax_class_title']);
        $Qclass->bindValue(':tax_class_description', $_POST['tax_class_description']);
        $Qclass->execute();

        if ($osC_Database->isError() === false) {
          if (isset($_GET['tcID']) && is_numeric($_GET['tcID'])) {
            $tax_class_id = $_GET['tcID'];
          } else {
            $tax_class_id = $osC_Database->nextID();
          }

          if ($Qclass->affectedRows()) {
            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        } else {
          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        tep_redirect(osc_href_link_admin(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $tax_class_id));
        break;
      case 'deleteconfirm':
        if (isset($_GET['tcID']) && is_numeric($_GET['tcID'])) {
          $error = false;

          $osC_Database->startTransaction();

          $Qrates = $osC_Database->query('delete from :table_tax_rates where tax_class_id = :tax_class_id');
          $Qrates->bindTable(':table_tax_rates', TABLE_TAX_RATES);
          $Qrates->bindInt(':tax_class_id', $_GET['tcID']);
          $Qrates->execute();

          if ($osC_Database->isError() === false) {
            $Qclass = $osC_Database->query('delete from :table_tax_class where tax_class_id = :tax_class_id');
            $Qclass->bindTable(':table_tax_class', TABLE_TAX_CLASS);
            $Qclass->bindInt(':tax_class_id', $_GET['tcID']);
            $Qclass->execute();

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

          tep_redirect(osc_href_link_admin(FILENAME_TAX_CLASSES, 'page=' . $_GET['page']));
        }
        break;
    }
  }

  if (!empty($entriesAction)) {
    switch ($entriesAction) {
      case 'save':
        if (isset($_GET['trID']) && is_numeric($_GET['trID'])) {
          $Qrate = $osC_Database->query('update :table_tax_rates set tax_zone_id = :tax_zone_id, tax_priority = :tax_priority, tax_rate = :tax_rate, tax_description = :tax_description, last_modified = now() where tax_rates_id = :tax_rates_id');
          $Qrate->bindInt(':tax_rates_id', $_GET['trID']);
        } else {
          $Qrate = $osC_Database->query('insert into :table_tax_rates (tax_zone_id, tax_class_id, tax_priority, tax_rate, tax_description, date_added) values (:tax_zone_id, :tax_class_id, :tax_priority, :tax_rate, :tax_description, now())');
          $Qrate->bindInt(':tax_class_id', $_GET['tcID']);
        }
        $Qrate->bindTable(':table_tax_rates', TABLE_TAX_RATES);
        $Qrate->bindInt(':tax_zone_id', $_POST['tax_zone_id']);
        $Qrate->bindInt(':tax_priority', $_POST['tax_priority']);
        $Qrate->bindValue(':tax_rate', $_POST['tax_rate']);
        $Qrate->bindValue(':tax_description', $_POST['tax_description']);
        $Qrate->execute();

        if ($osC_Database->isError() === false) {
          if (isset($_GET['trID']) && is_numeric($_GET['trID'])) {
            $tax_rate_id = $_GET['trID'];
          } else {
            $tax_rate_id = $osC_Database->nextID();
          }

          if ($Qrate->affectedRows()) {
            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        } else {
          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        tep_redirect(osc_href_link_admin(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $_GET['tcID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&trID=' . $tax_rate_id));
        break;
      case 'deleteconfirm':
        if (isset($_GET['trID']) && is_numeric($_GET['trID'])) {
          $Qrate = $osC_Database->query('delete from :table_tax_rates where tax_rates_id = :tax_rates_id');
          $Qrate->bindTable(':table_tax_rates', TABLE_TAX_RATES);
          $Qrate->bindInt(':tax_rates_id', $_GET['trID']);
          $Qrate->execute();

          if ($osC_Database->isError() === false) {
            if ($Qrate->affectedRows()) {
              $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
            } else {
              $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
            }
          } else {
            $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
          }

          tep_redirect(osc_href_link_admin(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tcID=' . $_GET['tcID'] . '&action=list&entriesPage=' . $_GET['entriesPage']));
        }
        break;
    }
  }

  switch ($action) {
    case 'list': $page_contents = 'tax_classes_listing.php'; break;
    default: $page_contents = 'tax_classes.php';
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
