<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Application_TaxClasses_Action_Save {
    public static function execute(OSCOM_ApplicationAbstract $application) {
      if ( isset($_GET['id']) && is_numeric($_GET['id']) ) {
        $application->setPageContent('edit.php');
      } else {
        $application->setPageContent('new.php');
      }

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        $data = array('title' => $_POST['tax_class_title'],
                      'description' => $_POST['tax_class_description']);

        if ( OSCOM_Site_Admin_Application_TaxClasses_TaxClasses::save((isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null), $data) ) {
          OSCOM_Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_success_action_performed'), 'success');
        } else {
          OSCOM_Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_action_not_performed'), 'error');
        }

        osc_redirect_admin(OSCOM::getLink());
      }
    }
  }
?>
