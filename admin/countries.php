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

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
          $Qcountry = $osC_Database->query('update :table_countries set countries_name = :countries_name, countries_iso_code_2 = :countries_iso_code_2, countries_iso_code_3 = :countries_iso_code_3, address_format_id = :address_format_id where countries_id = :countries_id');
          $Qcountry->bindInt(':countries_id', $_GET['cID']);
        } else {
          $Qcountry = $osC_Database->query('insert into :table_countries (countries_name, countries_iso_code_2, countries_iso_code_3, address_format_id) values (:countries_name, :countries_iso_code_2, :countries_iso_code_3, :address_format_id)');
        }
        $Qcountry->bindTable(':table_countries', TABLE_COUNTRIES);
        $Qcountry->bindValue(':countries_name', $_POST['countries_name']);
        $Qcountry->bindValue(':countries_iso_code_2', $_POST['countries_iso_code_2']);
        $Qcountry->bindValue(':countries_iso_code_3', $_POST['countries_iso_code_3']);
        $Qcountry->bindInt(':address_format_id', $_POST['address_format_id']);
        $Qcountry->execute();

        if ($osC_Database->isError() === false) {
          if ($Qcountry->affectedRows()) {
            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        } else {
          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
          tep_redirect(osc_href_link_admin(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $_GET['cID']));
        } else {
          tep_redirect(osc_href_link_admin(FILENAME_COUNTRIES));
        }

        break;
      case 'deleteconfirm':
        if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
          $error = false;

          $osC_Database->startTransaction();

          $Qzones = $osC_Database->query('delete from :table_zones where zone_country_id = :zone_country_id');
          $Qzones->bindTable(':table_zones', TABLE_ZONES);
          $Qzones->bindInt(':zone_country_id', $_GET['cID']);
          $Qzones->execute();

          if ($osC_Database->isError() === false) {
            $Qcountry = $osC_Database->query('delete from :table_countries where countries_id = :countries_id');
            $Qcountry->bindTable(':table_countries', TABLE_COUNTRIES);
            $Qcountry->bindInt(':countries_id', $_GET['cID']);
            $Qcountry->execute();

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
        }

        tep_redirect(osc_href_link_admin(FILENAME_COUNTRIES, 'page=' . $_GET['page']));
        break;
    }
  }

  $page_contents = 'countries.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
