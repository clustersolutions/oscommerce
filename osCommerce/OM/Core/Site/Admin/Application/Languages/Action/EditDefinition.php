<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Admin\Application\Languages\Action;

  use osCommerce\OM\ApplicationAbstract;
  use osCommerce\OM\Site\Admin\Application\Languages\Languages;
  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;

  class EditDefinition {
    public static function execute(ApplicationAbstract $application) {
      $application->setPageContent('definitions_edit.php');

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        if ( Languages::updateDefinitions($_GET['id'], $_GET['group'], $_POST['def']) ) {
          Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_success_action_performed'), 'success');
        } else {
          Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_action_not_performed'), 'error');
        }

        osc_redirect_admin(OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&group=' . $_GET['group']));
      }
    }
  }
?>
