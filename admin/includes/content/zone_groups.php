<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Zone_groups extends osC_Template {

/* Private variables */

    var $_module = 'zone_groups',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'zone_groups.php';

/* Class constructor */

    function osC_Content_Zone_groups() {
      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['entriesAction'])) {
        $_GET['entriesAction'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'save':
            $this->_processGroup();
            break;

          case 'deleteconfirm':
            $this->_deleteGroup();
            break;

          case 'list':
            $this->_page_contents = 'zone_groups_entries.php';
            break;
        }
      }

      if (!empty($_GET['entriesAction'])) {
        switch ($_GET['entriesAction']) {
          case 'save':
            $this->_processEntry();
            break;

          case 'deleteconfirm':
            $this->_deleteEntry();
            break;
        }
      }
    }

/* Private methods */

    function _processGroup() {
      global $osC_Database, $osC_MessageStack;

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
          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
        }
      } else {
        $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . '&zID=' . $zone_id));
    }

    function _deleteGroup() {
      global $osC_Database, $osC_MessageStack;

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

          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_Database->rollbackTransaction();

          $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
      }
    }

    function _processEntry() {
      global $osC_Database, $osC_MessageStack;

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

      if (!$osC_Database->isError()) {
        if (isset($_GET['zeID']) && is_numeric($_GET['zeID'])) {
          $entry_id = $_GET['zeID'];
        } else {
          $entry_id = $osC_Database->nextID();
        }

        if ($Qentry->affectedRows()) {
          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
        }
      } else {
        $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list&zeID=' . $entry_id));
    }

    function _deleteEntry() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['zeID']) && is_numeric($_GET['zeID'])) {
        $Qentry = $osC_Database->query('delete from :table_zones_to_geo_zones where association_id = :association_id');
        $Qentry->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qentry->bindInt(':association_id', $_GET['zeID']);
        $Qentry->execute();

        if (!$osC_Database->isError()) {
          if ($Qentry->affectedRows()) {
            $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        } else {
          $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . '&zID=' . $_GET['zID'] . '&action=list'));
      }
    }
  }
?>
