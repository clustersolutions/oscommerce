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

  class osC_Application_Zone_groups_Actions_entry_save extends osC_Application_Zone_groups {
    public function __construct() {
      global $osC_Language, $osC_MessageStack;

      parent::__construct();

      if ( isset($_GET['zeID']) && is_numeric($_GET['zeID']) ) {
        $this->_page_contents = 'entries_edit.php';
      } else {
        $this->_page_contents = 'entries_new.php';
      }

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        $data = array('group_id' => $_GET[$this->_module],
                      'country_id' => $_POST['zone_country_id'],
                      'zone_id' => $_POST['zone_id']);

        if ( osC_ZoneGroups_Admin::saveEntry((isset($_GET['zeID']) && is_numeric($_GET['zeID']) ? $_GET['zeID'] : null), $data) ) {
          $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
        } else {
          $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
        }

        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module]));
      }
    }
  }
?>
