<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Application_Languages_Action_Export {
    public static function execute(OSCOM_ApplicationAbstract $application) {
      $application->setPageContent('export.php');

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        OSCOM_Site_Admin_Application_Languages_Languages::export($_GET['id'], $_POST['groups'], (isset($_POST['include_data']) && ($_POST['include_data'] == 'on')));
      }
    }
  }
?>
