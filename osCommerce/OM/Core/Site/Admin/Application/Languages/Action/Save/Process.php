<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Action\Save;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $data = array('id' => $_GET['id'],
                    'name' => $_POST['name'],
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
                    'sort_order' => $_POST['sort_order'],
                    'set_default' => (isset($_POST['default']) && ($_POST['default'] == 'on')));

      if ( Languages::update($data) ) {
        Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_success_action_performed'), 'success');
      } else {
        Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_action_not_performed'), 'error');
      }

      osc_redirect_admin(OSCOM::getLink());
    }
  }
?>
