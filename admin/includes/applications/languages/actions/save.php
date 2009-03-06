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

  class osC_Application_Languages_Actions_save extends osC_Application_Languages {
    public function __construct() {
      global $osC_Language, $osC_MessageStack;

      parent::__construct();

      $this->_page_contents = 'edit.php';

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        $data = array('name' => $_POST['name'],
                      'code' => $_POST['code'],
                      'locale' => $_POST['locale'],
                      'charset' => $_POST['charset'],
                      'date_format_short' => $_POST['date_format_short'],
                      'date_format_long' => $_POST['date_format_long'],
                      'time_format' => $_POST['time_format'],
                      'text_direction' => $_POST['text_direction'],
                      'currencies_id' => $_POST['currencies_id'],
                      'numeric_separator_decimal' => $_POST['numeric_separator_decimal'],
                      'numeric_separator_thousands' => $_POST['numeric_separator_thousands'],
                      'parent_id' => $_POST['parent_id'],
                      'sort_order' => $_POST['sort_order']);

        if ( osC_Languages_Admin::update($_GET['lID'], $data, (isset($_POST['default']) && ($_POST['default'] == 'on'))) ) {
          $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
        } else {
          $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
        }

        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
      }
    }
  }
?>
