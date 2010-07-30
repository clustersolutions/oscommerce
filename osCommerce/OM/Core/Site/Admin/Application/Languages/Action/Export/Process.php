<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Action\Export;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      Languages::export($_GET['id'], $_POST['groups'], (isset($_POST['include_data']) && ($_POST['include_data'] == 'on')));
    }
  }
?>
