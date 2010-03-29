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

  class osC_Application_Configuration_Actions_batch_save extends osC_Application_Configuration {
    public function __construct() {
      global $osC_Language, $osC_MessageStack;

      parent::__construct();

      if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
        $this->_page_contents = 'batch_edit.php';

        if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
          $error = false;

          foreach ( $_POST['configuration'] as $key => $param ) {
            if ( !osC_Configuration_Admin::save(array($key => $param)) ) {
              $error = true;
              break;
            }
          }

          if ( $error === false ) {
            $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
          } else {
            $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
          }

          osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&gID=' . $_GET['gID']));
        }
      }
    }
  }
?>
