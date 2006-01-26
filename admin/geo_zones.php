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
        if (isset($_GET['zID']) && is_numeric($_GET['zID'])) {
          $Qzone = $osC_Database->query('update :table_geo_zones set geo_zone_name = :geo_zone_name, geo_zone_description = :geo_zone_description, last_modified = now() where geo_zone_id = :geo_zone_id');
          $Qzone->bindInt(':geo_zone_id', $_GET['zID']);
        } else {
          $Qzone = $osC_Database->query('insert into :table_geo_zones (geo_zone_name, geo_zone_description, date_added) values (:geo_zone_name, :geo_zone_description, now())');
        }
        $Qzone->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
        $Qzone->bindValue(':geo_zone_name', $_POST['geo_zone_name']);
        $Qzone->bindValue(':geo_zone_description', $_POST['geo_zone_description']);
        $Qzone->execute();

        if ($osC_Database->isError() === false) {
          if (isset($_GET['zID']) && is_numeric($_GET['zID'])) {
            $zone_id = $_GET['zID'];
          } else {
            $zone_id = $osC_Database->nextID();
          }

          if ($Qzone->affectedRows()) {
            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        } else {
          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        tep_redirect(tep_href_link(FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $zone_id));
        break;
      case 'deleteconfirm':
        if (isset($_GET['zID']) && is_numeric($_GET['zID'])) {
          $error = false;

          $osC_Database->startTransaction();

          $Qentry = $osC_Database->query('delete from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id');
          $Qentry->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
          $Qentry->bindInt(':geo_zone_id', $_GET['zID']);
          $Qentry->execute();

          if ($osC_Database->isError() === false) {
            $Qzone = $osC_Database->query('delete from :table_geo_zones where geo_zone_id = :geo_zone_id');
            $Qzone->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
            $Qzone->bindInt(':geo_zone_id', $_GET['zID']);
            $Qzone->execute();

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

          tep_redirect(tep_href_link(FILENAME_GEO_ZONES, 'page=' . $_GET['page']));
        }
        break;
    }
  }

  if (!empty($entriesAction)) {
    switch ($entriesAction) {
      case 'zeSave':
        if (isset($_GET['zeID']) && is_numeric($_GET['zeID'])) {
          $Qentry = $osC_Database->query('update :table_zones_to_geo_zones set zone_country_id = :zone_country_id, zone_id = :zone_id, last_modified = now() where association_id = :association_id');
          $Qentry->bindInt(':association_id', $_GET['zeID']);
        } else {
          $Qentry = $osC_Database->query('insert into :table_zones_to_geo_zones (zone_country_id, zone_id, geo_zone_id, date_added) values (:zone_country_id, :zone_id, :geo_zone_id, now())');
          $Qentry->bindInt(':geo_zone_id', $_GET['zID']);
        }
        $Qentry->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qentry->bindInt(':zone_country_id', $_POST['zone_country_id']);
        $Qentry->bindInt(':zone_id', $_POST['zone_id']);
        $Qentry->execute();

        if ($osC_Database->isError() === false) {
          if (isset($_GET['zeID']) && is_numeric($_GET['zeID'])) {
            $entry_id = $_GET['zeID'];
          } else {
            $entry_id = $osC_Database->nextID();
          }

          if ($Qentry->affectedRows()) {
            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        } else {
          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        tep_redirect(tep_href_link(FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&zeID=' . $entry_id));
        break;
      case 'zeDeleteConfirm':
        if (isset($_GET['zeID']) && is_numeric($_GET['zeID'])) {
          $Qentry = $osC_Database->query('delete from :table_zones_to_geo_zones where association_id = :association_id');
          $Qentry->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
          $Qentry->bindInt(':association_id', $_GET['zeID']);
          $Qentry->execute();

          if ($osC_Database->isError() === false) {
            if ($Qentry->affectedRows()) {
              $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
            } else {
              $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
            }
          } else {
            $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
          }

          tep_redirect(tep_href_link(FILENAME_GEO_ZONES, 'page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list&entriesPage=' . $_GET['entriesPage']));
        }
        break;
    }
  }

  switch ($action) {
    case 'list': $page_contents = 'geo_zones_listing.php'; break;
    default: $page_contents = 'geo_zones.php';
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
