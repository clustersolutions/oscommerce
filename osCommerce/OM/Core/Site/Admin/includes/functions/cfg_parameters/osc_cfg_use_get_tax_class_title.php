<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  function osc_cfg_use_get_tax_class_title($id) {
    $OSCOM_Database = Registry::get('Database');
    $OSCOM_Language = Registry::get('Language');

    if ( $id < 1 ) {
      return OSCOM::getDef('parameter_none');
    }

    $Qclass = $OSCOM_Database->query('select tax_class_title from :table_tax_class where tax_class_id = :tax_class_id');
    $Qclass->bindInt(':tax_class_id', $id);
    $Qclass->execute();

    return $Qclass->value('tax_class_title');
  }
?>
