<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\Action;

  use osCommerce\OM\Core\ApplicationAbstract;

  class Save {
    public static function execute(ApplicationAbstract $application) {
      if ( isset($_GET['id']) && is_numeric($_GET['id']) ) {
        $application->setPageContent('edit.php');
      } else {
        $application->setPageContent('new.php');
      }
    }
  }
?>
