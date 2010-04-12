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

  class osC_Application_Countries_Actions_save extends osC_Application_Countries {
    public function __construct() {
      global $osC_Language, $osC_MessageStack;

      parent::__construct();

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
          $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
        } else {
          $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
        }

        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
      }
    }
  }
?>
