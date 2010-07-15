<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\ZoneGroups;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class EntrySave {
    public static function execute(ApplicationAbstract $application) {
      if ( isset($_GET['zID']) && is_numeric($_GET['zID']) ) {
        $application->setPageContent('entries_edit.php');
      } else {
        $application->setPageContent('entries_new.php');
      }

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        $data = array('group_id' => $_GET['id'],
                      'country_id' => $_POST['zone_country_id'],
                      'zone_id' => $_POST['zone_id']);

        if ( ZoneGroups::saveEntry((isset($_GET['zID']) && is_numeric($_GET['zID']) ? $_GET['zID'] : null), $data) ) {
          Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_success_action_performed'), 'success');
        } else {
          Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_action_not_performed'), 'error');
        }

        osc_redirect_admin(OSCOM::getLink(null, null, 'id=' . $_GET['id']));
      }
    }
  }
?>
