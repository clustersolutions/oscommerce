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

  function osc_cfg_use_get_order_status_title($id) {
    $OSCOM_PDO = Registry::get('PDO');
    $OSCOM_Language = Registry::get('Language');

    if ( $id < 1 ) {
      return OSCOM::getDef('default_entry');
    }

    $Qstatus = $OSCOM_PDO->prepare('select orders_status_name from :table_orders_status where orders_status_id = :orders_status_id and language_id = :language_id');
    $Qstatus->bindInt(':orders_status_id', $id);
    $Qstatus->bindInt(':language_id', $OSCOM_Language->getID());
    $Qstatus->execute();

    return $Qstatus->value('orders_status_name');
  }
?>
