<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/countries.php');

  class osC_Content_Countries extends osC_Template {

/* Private variables */

    var $_module = 'countries',
        $_page_title,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Countries() {
      global $osC_Language, $osC_MessageStack;

      $this->_page_title = $osC_Language->get('heading_title');

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      if (!empty($_GET[$this->_module]) && is_numeric($_GET[$this->_module])) {
        $this->_page_contents = 'zones.php';
        $this->_page_title .= ': ' . osC_Address::getCountryName($_GET[$this->_module]);
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'save':
            if ( isset($_GET['cID']) && is_numeric($_GET['cID']) ) {
              $this->_page_contents = 'edit.php';
            } else {
              $this->_page_contents = 'new.php';
            }

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('name' => $_POST['countries_name'],
                            'iso_code_2' => $_POST['countries_iso_code_2'],
                            'iso_code_3' => $_POST['countries_iso_code_3'],
                            'address_format' => $_POST['address_format']);

              if ( osC_Countries_Admin::save((isset($_GET['cID']) && is_numeric($_GET['cID']) ? $_GET['cID'] : null), $data) ) {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;

          case 'zoneSave':
            if ( isset($_GET['zID']) && is_numeric($_GET['zID']) ) {
              $this->_page_contents = 'zones_edit.php';
            } else {
              $this->_page_contents = 'zones_new.php';
            }

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('name' => $_POST['zone_name'],
                            'code' => $_POST['zone_code'],
                            'country_id' => $_GET[$this->_module]);

              if ( osC_Countries_Admin::saveZone((isset($_GET['zID']) && is_numeric($_GET['zID']) ? $_GET['zID'] : null), $data) ) {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module] . '&page=' . $_GET['page']));
            }

            break;

          case 'delete':
            $this->_page_contents = 'delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Countries_Admin::delete($_GET['cID']) ) {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;

          case 'zoneDelete':
            $this->_page_contents = 'zones_delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Countries_Admin::deleteZone($_GET['zID']) ) {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module] . '&page=' . $_GET['page']));
            }

            break;

          case 'batchDelete':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              $this->_page_contents = 'batch_delete.php';

              if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
                $error = false;

                foreach ($_POST['batch'] as $id) {
                  if ( !osC_Countries_Admin::delete($id) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
                } else {
                  $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
                }

                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
              }
            }

            break;

          case 'batchDeleteZones':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              $this->_page_contents = 'zones_batch_delete.php';

              if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
                $error = false;

                foreach ($_POST['batch'] as $id) {
                  if ( !osC_Countries_Admin::deleteZone($id) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
                } else {
                  $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
                }

                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module] . '&page=' . $_GET['page']));
              }
            }

            break;
        }
      }
    }
  }
?>
