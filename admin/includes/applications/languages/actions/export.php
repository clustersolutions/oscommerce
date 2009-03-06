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

  class osC_Application_Languages_Actions_export extends osC_Application_Languages {
    public function __construct() {
      parent::__construct();

      $this->_page_contents = 'export.php';

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        osC_Languages_Admin::export($_GET['lID'], $_POST['groups'], (isset($_POST['include_data']) && ($_POST['include_data'] == 'on')));
      }
    }
  }
?>
