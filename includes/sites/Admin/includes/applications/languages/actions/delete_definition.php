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

  class osC_Application_Languages_Actions_delete_definition extends osC_Application_Languages {
    public function __construct() {
      global $osC_Language, $osC_MessageStack;

      parent::__construct();

      $this->_page_contents = 'definitions_delete.php';

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        if ( osC_Languages_Admin::deleteDefinitions($_GET[$this->_module], $_GET['group'], array($_GET['dID'])) ) {
          $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
        } else {
          $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
        }

        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module]) . (isset($_GET['group']) ? '&group=' . $_GET['group'] : ''));
      }
    }
  }
?>
