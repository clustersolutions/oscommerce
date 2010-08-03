<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  function osc_cfg_set_tax_classes_pull_down_menu($default, $key = null) {
    $OSCOM_Language = Registry::get('Language');
    $OSCOM_Database = Registry::get('Database');

    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';

    $tax_class_array = array(array('id' => '0',
                                   'text' => OSCOM::getDef('parameter_none')));

    $Qclasses = $OSCOM_Database->query('select tax_class_id, tax_class_title from :table_tax_class order by tax_class_title');
    $Qclasses->execute();

    while ( $Qclasses->next() ) {
      $tax_class_array[] = array('id' => $Qclasses->valueInt('tax_class_id'),
                                 'text' => $Qclasses->value('tax_class_title'));
    }

    return osc_draw_pull_down_menu($name, $tax_class_array, $default);
  }
?>
