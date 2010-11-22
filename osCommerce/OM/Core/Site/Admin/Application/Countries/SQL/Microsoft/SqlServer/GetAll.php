<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\Microsoft\SqlServer;

  use osCommerce\OM\Core\Registry;

  class GetAll {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('MSSQL');

      $result = array();

      $Q = $OSCOM_Database->query('EXEC CountriesGetAll :batch_pageset, :batch_max_results');
      $Q->bindInt(':batch_pageset', $data['batch_pageset']);
      $Q->bindInt(':batch_max_results', $data['batch_max_results']);
      $Q->execute();

      while ( $Q->next() ) {
        $result['entries'][] = $Q->toArray();
      }

      $Q->nextResultSet();

      $result['total'] = $Q->value('total');

      $Q->freeResult();

      return $result;
    }
  }
?>
