<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Zones extends osC_Template {

/* Private variables */

    var $_module = 'zones',
        $_page_title,
        $_page_contents = 'zones.php';

/* Class constructor */

    function osC_Content_Zones() {
      $this->_page_title = HEADING_TITLE;

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
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

      if (!$osC_Database->isError()) {
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

    function _delete() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['zID']) && is_numeric($_GET['zID'])) {
        $Qzone = $osC_Database->query('delete from :table_zones where zone_id = :zone_id');
        $Qzone->bindTable(':table_zones', TABLE_ZONES);
        $Qzone->bindInt(':zone_id', $_GET['zID']);
        $Qzone->execute();

        if (!$osC_Database->isError()) {
          if ($Qzone->affectedRows()) {
            $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        } else {
          $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
      }
    }
  }
?>
