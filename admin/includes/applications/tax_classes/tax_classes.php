<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/applications/tax_classes/classes/tax_classes.php');
  require('includes/classes/tax.php');

  class osC_Application_Tax_classes extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'tax_classes',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Language, $osC_MessageStack;

      $this->_page_title = $osC_Language->get('heading_title');

      if (!empty($_GET[$this->_module]) && is_numeric($_GET[$this->_module])) {
        $this->_page_contents = 'entries.php';
        $this->_page_title .= ': ' . osC_TaxClasses_Admin::get($_GET[$this->_module], 'tax_class_title');
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
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

              if ( osC_TaxClasses_Admin::saveEntry((isset($_GET['trID']) && is_numeric($_GET['trID']) ? $_GET['trID'] : null), $data) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module] . '&page=' . $_GET['page']));
            }

            break;

          case 'entryDelete':
            $this->_page_contents = 'entries_delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_TaxClasses_Admin::deleteEntry($_GET['trID']) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module] . '&page=' . $_GET['page']));
            }

            break;

          case 'batchDeleteEntries':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              $this->_page_contents = 'entries_batch_delete.php';

              if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
                $error = false;

                foreach ($_POST['batch'] as $id) {
                  if ( !osC_TaxClasses_Admin::deleteEntry($id) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
                } else {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
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
