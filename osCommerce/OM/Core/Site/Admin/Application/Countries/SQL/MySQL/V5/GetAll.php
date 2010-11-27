<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\MySQL\V5;

  use osCommerce\OM\Core\Registry;

  class GetAll {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $result = array();

      $Qcountries = $OSCOM_Database->prepare('CALL CountriesGetAll(:batch_pageset, :batch_max_results)');

      if ( $data['batch_pageset'] !== -1 ) {
        $Qcountries->bindInt(':batch_pageset', $OSCOM_Database->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qcountries->bindInt(':batch_max_results', $data['batch_max_results']);
      } else {
        $Qcountries->bindNull(':batch_pageset');
        $Qcountries->bindNull(':batch_max_results');
      }

      $Qcountries->execute();

      $result['entries'] = $Qcountries->fetchAll();

      $Qcountries->nextRowset();

      $result['total'] = $Qcountries->fetchColumn(0);

      unset($Qcountries);

      return $result;
    }
  }
?>
