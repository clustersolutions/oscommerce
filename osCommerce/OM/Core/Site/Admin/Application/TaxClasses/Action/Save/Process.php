<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\TaxClasses\Action\Save;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Site\Admin\Application\TaxClasses\TaxClasses;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $data = array('title' => $_POST['tax_class_title'],
                    'description' => $_POST['tax_class_description']);

      if ( TaxClasses::save((isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null), $data) ) {
        Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_success_action_performed'), 'success');
      } else {
        Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_action_not_performed'), 'error');
      }

      osc_redirect_admin(OSCOM::getLink());
    }
  }
?>
