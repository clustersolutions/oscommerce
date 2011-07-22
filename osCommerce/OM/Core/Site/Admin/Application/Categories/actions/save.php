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

  class osC_Application_Categories_Actions_save extends osC_Application_Categories {
    public function __construct() {
      global $osC_Language, $osC_MessageStack;

      parent::__construct();

      if ( isset($_GET['cID']) && is_numeric($_GET['cID']) ) {
        $this->_page_contents = 'edit.php';
      } else {
        $this->_page_contents = 'new.php';
      }

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        $data = array('name' => $_POST['categories_name'],
                      'image' => isset($_FILES['categories_image']) ? $_FILES['categories_image'] : null,
                      'sort_order' => $_POST['sort_order']);

        if ( !isset($_GET['cID']) ) {
          $data['parent_id'] = $_POST['parent_id'];
        }

        $error = false;

        foreach ( $data['name'] as $key => $value ) {
          if ( empty($value) ) {
            $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_warning_category_name_empty'), $osC_Language->getData($key, 'name')), 'warning');

            $error = true;
          }
        }

        if ( $error === false ) {
          if ( osC_Categories_Admin::save((isset($_GET['cID']) && is_numeric($_GET['cID']) ? $_GET['cID'] : null), $data) ) {
            $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
          } else {
            $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
          }

          osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module]));
        }
      }
    }
  }
?>
