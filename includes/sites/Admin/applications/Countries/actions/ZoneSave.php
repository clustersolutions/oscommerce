<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Application_Countries_Action_ZoneSave {
    public static function execute(OSCOM_ApplicationAbstract $application) {
      if ( isset($_GET['zID']) && is_numeric($_GET['zID']) ) {
        $application->setPageContent('zones_edit.php');
      } else {
        $application->setPageContent('zones_new.php');
      }

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        $data = array('name' => $_POST['zone_name'],
                      'code' => $_POST['zone_code'],
                      'country_id' => $_GET['id']);

        if ( OSCOM_Site_Admin_Application_Countries_Countries::saveZone((isset($_GET['zID']) && is_numeric($_GET['zID']) ? $_GET['zID'] : null), $data) ) {
          OSCOM_Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_success_action_performed'), 'success');
        } else {
          OSCOM_Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_action_not_performed'), 'error');
        }

        osc_redirect_admin(OSCOM::getLink(null, null, 'id=' . $_GET['id']));
      }
    }
  }
?>
