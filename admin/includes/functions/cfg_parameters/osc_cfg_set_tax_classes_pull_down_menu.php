<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_set_tax_classes_pull_down_menu($default, $key = null) {
    global $osC_Database, $osC_Language;

    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';

    $tax_class_array = array(array('id' => '0',
                                   'text' => $osC_Language->get('parameter_none')));

    $Qclasses = $osC_Database->query('select tax_class_id, tax_class_title from :table_tax_class order by tax_class_title');
    $Qclasses->bindTable(':table_tax_class', TABLE_TAX_CLASS);
    $Qclasses->execute();

    while ($Qclasses->next()) {
      $tax_class_array[] = array('id' => $Qclasses->valueInt('tax_class_id'),
                                 'text' => $Qclasses->value('tax_class_title'));
    }

    return osc_draw_pull_down_menu($name, $tax_class_array, $default);
  }
?>
