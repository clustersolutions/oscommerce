<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Website\Application\Developers\Action\CLA;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\PDO;

  class Success {
    public static function execute(ApplicationAbstract $application) {
      $application->developersPage = 'cla_success';

      $OSCOM_PDO = PDO::initialize();

      $Qlist = $OSCOM_PDO->query('select profile_username, profile_id, github_profile from osc_cla order by date_created desc limit 20');
      $Qlist->setCache('cla_list');
      $Qlist->execute();

      $application->claList = $Qlist->fetchAll();
    }
  }
?>
