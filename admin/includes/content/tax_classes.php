<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/tax.php');

  class osC_Content_Tax_classes extends osC_Template {

/* Private variables */

    var $_module = 'tax_classes',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Tax_classes() {
      global $osC_MessageStack;

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      if (!empty($_GET[$this->_module]) && is_numeric($_GET[$this->_module])) {
        $this->_page_contents = 'entries.php';
        $this->_page_title .= ': ' . osC_Tax_Admin::getData($_GET[$this->_module], 'tax_class_title');
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'save':
            if ( isset($_GET['tcID']) && is_numeric($_GET['tcID']) ) {
              $this->_page_contents = 'edit.php';
            } else {
              $this->_page_contents = 'new.php';
            }

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('title' => $_POST['tax_class_title'],
                            'description' => $_POST['tax_class_description']);

              if ( osC_Tax_Admin::save((isset($_GET['tcID']) && is_numeric($_GET['tcID']) ? $_GET['tcID'] : null), $data) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;

          case 'delete':
            $this->_page_contents = 'delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Tax_Admin::delete($_GET['tcID']) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;

          case 'entrySave':
            if ( isset($_GET['trID']) && is_numeric($_GET['trID']) ) {
              $this->_page_contents = 'entries_edit.php';
            } else {
              $this->_page_contents = 'entries_new.php';
            }

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('zone_id' => $_POST['tax_zone_id'],
                            'rate' => $_POST['tax_rate'],
                            'description' => $_POST['tax_description'],
                            'priority' => $_POST['tax_priority'],
                            'rate' => $_POST['tax_rate'],
                            'tax_class_id' => $_GET[$this->_module]);

              if ( osC_Tax_Admin::saveEntry((isset($_GET['trID']) && is_numeric($_GET['trID']) ? $_GET['trID'] : null), $data) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module] . '&page=' . $_GET['page']));
            }

            break;

          case 'entryDelete':
            $this->_page_contents = 'entries_delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Tax_Admin::deleteEntry($_GET['trID']) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
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
                  if ( !osC_Tax_Admin::delete($id) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
                } else {
                  $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
                }

                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
              }
            }

            break;

          case 'batchDeleteEntries':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              $this->_page_contents = 'entries_batch_delete.php';

              if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
                $error = false;

                foreach ($_POST['batch'] as $id) {
                  if ( !osC_Tax_Admin::deleteEntry($id) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
                } else {
                  $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
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
