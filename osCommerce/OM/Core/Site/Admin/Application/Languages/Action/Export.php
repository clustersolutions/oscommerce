<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;

  class Export {
    public static function execute(ApplicationAbstract $application) {
      $application->setPageContent('export.php');

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        Languages::export($_GET['id'], $_POST['groups'], (isset($_POST['include_data']) && ($_POST['include_data'] == 'on')));
      }
    }
  }
?>
