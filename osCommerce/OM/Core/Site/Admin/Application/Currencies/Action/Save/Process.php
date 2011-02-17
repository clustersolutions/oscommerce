<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Currencies\Action\Save;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Site\Admin\Application\Currencies\Currencies;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $data = array('id' => (isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null),
                    'title' => $_POST['title'],
                    'code' => $_POST['code'],
                    'symbol_left' => $_POST['symbol_left'],
                    'symbol_right' => $_POST['symbol_right'],
                    'decimal_places' => $_POST['decimal_places'],
                    'value' => $_POST['value'],
                    'set_default' => ((isset($_POST['default']) && ($_POST['default'] == 'on')) || (isset($_POST['is_default']) && ($_POST['is_default'] == 'true') && ($_POST['code'] != DEFAULT_CURRENCY))));

      if ( Currencies::save($data) ) {
        Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_success_action_performed'), 'success');
      } else {
        Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_action_not_performed'), 'error');
      }

      osc_redirect_admin(OSCOM::getLink());
    }
  }
?>
