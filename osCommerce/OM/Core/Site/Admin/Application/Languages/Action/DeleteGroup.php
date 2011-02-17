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

  class DeleteGroup {
    public static function execute(ApplicationAbstract $application) {
      if ( Languages::isGroup($_GET['id'], $_GET['group']) ) {
        $application->setPageContent('groups_delete.php');
      }
    }
  }
?>
