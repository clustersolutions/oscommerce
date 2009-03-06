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

  class osC_Application_Administrators_Actions_save extends osC_Application_Administrators {
    public function __construct() {
      global $osC_Language, $osC_MessageStack;

      parent::__construct();

      if ( isset($_GET['aID']) && is_numeric($_GET['aID']) ) {
        $this->_page_contents = 'edit.php';
      } else {
        $this->_page_contents = 'new.php';
      }

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        $data = array('username' => $_POST['user_name'],
                      'password' => $_POST['user_password']);

        switch ( osC_Administrators_Admin::save((isset($_GET['aID']) && is_numeric($_GET['aID']) ? $_GET['aID'] : null), $data, (isset($_POST['modules']) ? $_POST['modules'] : null)) ) {
          case 1:
            if ( isset($_GET['aID']) && is_numeric($_GET['aID']) && ($_GET['aID'] == $_SESSION['admin']['id']) ) {
              $_SESSION['admin']['access'] = osC_Access::getUserLevels($_GET['aID']);
            }

            $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');

            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));

            break;

          case -1:
            $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');

            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));

            break;

          case -2:
            $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_username_already_exists'), 'error');

            break;
        }
      }
    }
  }
?>
