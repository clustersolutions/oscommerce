<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Application_Administrators_Action_BatchSave {
    public function execute(OSCOM_ApplicationAbstract $application) {
      if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
        $application->setPageContent('batch_edit.php');

        if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
          $error = false;

          foreach ( $_POST['batch'] as $id ) {
            if ( !OSCOM_Site_Admin_Application_Administrators_Administrators::setAccessLevels($id, $_POST['modules'], $_POST['mode']) ) {
              $error = true;
              break;
            }
          }

          if ( $error === false ) {
            OSCOM_Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_success_action_performed'), 'success');

            if ( in_array($_SESSION[OSCOM::getSite()]['id'], $_POST['batch']) ) {
              $_SESSION[OSCOM::getSite()]['access'] = osC_Access::getUserLevels($_SESSION[OSCOM::getSite()]['id']);
            }
          } else {
            OSCOM_Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_action_not_performed'), 'error');
          }

          osc_redirect_admin(OSCOM::getLink());
        }
      }
    }
  }
?>
