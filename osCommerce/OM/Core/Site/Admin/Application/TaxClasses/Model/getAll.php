<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\TaxClasses\Model;

  use osCommerce\OM\Core\OSCOM;

  class getAll {
    public static function execute($pageset = 1) {
      $data = array('batch_pageset' => $pageset,
                    'batch_max_results' => MAX_DISPLAY_SEARCH_RESULTS);

      if ( !is_numeric($data['batch_pageset']) || (floor($data['batch_pageset']) != $data['batch_pageset']) ) {
        $data['batch_pageset'] = 1;
      }

      return OSCOM::callDB('Admin\TaxClasses\GetAll', $data);
    }
  }
?>
