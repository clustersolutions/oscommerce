<?php
/*
  $Id: zones.php,v 1.25 2004/10/28 18:50:13 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $selected_box = 'taxes';

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        if (isset($_GET['zID']) && is_numeric($_GET['zID'])) {
          $Qzone = $osC_Database->query('update :table_zones set zone_name = :zone_name, zone_code = :zone_code, zone_country_id = :zone_country_id where zone_id = :zone_id');
          $Qzone->bindInt(':zone_id', $_GET['zID']);
        } else {
          $Qzone = $osC_Database->query('insert into :table_zones (zone_name, zone_code, zone_country_id) values (:zone_name, :zone_code, :zone_country_id)');
        }
        $Qzone->bindTable(':table_zones', TABLE_ZONES);
        $Qzone->bindValue(':zone_name', $_POST['zone_name']);
        $Qzone->bindValue(':zone_code', $_POST['zone_code']);
        $Qzone->bindInt(':zone_country_id', $_POST['zone_country_id']);
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

        tep_redirect(tep_href_link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&zID=' . $zone_id));
        break;
      case 'deleteconfirm':
        if (isset($_GET['zID']) && is_numeric($_GET['zID'])) {
          $Qzone = $osC_Database->query('delete from :table_zones where zone_id = :zone_id');
          $Qzone->bindTable(':table_zones', TABLE_ZONES);
          $Qzone->bindInt(':zone_id', $_GET['zID']);
          $Qzone->execute();

          if ($osC_Database->isError() === false) {
            if ($Qzone->affectedRows()) {
              $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
            } else {
              $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
            }
          } else {
            $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
          }

          tep_redirect(tep_href_link(FILENAME_ZONES, 'page=' . $_GET['page']));
        }
        break;
    }
  }

  $page_contents = 'zones.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
