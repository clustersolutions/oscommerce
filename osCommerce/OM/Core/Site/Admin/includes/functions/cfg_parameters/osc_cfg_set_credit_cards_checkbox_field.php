<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\Registry;

  function osc_cfg_set_credit_cards_checkbox_field($default, $key = null) {
    $OSCOM_PDO = Registry::get('PDO');

    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . '][]';

    $cc_array = array();

    $Qcc = $OSCOM_PDO->prepare('select id, credit_card_name from :table_credit_cards where credit_card_status = :credit_card_status order by sort_order, credit_card_name');
    $Qcc->bindInt(':credit_card_status', 1);
    $Qcc->execute();

    while ( $Qcc->next() ) {
      $cc_array[] = array('id' => $Qcc->valueInt('id'),
                          'text' => $Qcc->value('credit_card_name'));
    }

    return HTML::checkboxField($name, $cc_array, explode(',', $default), null, '<br />');
  }
?>
