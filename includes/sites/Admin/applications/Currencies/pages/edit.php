<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(OSCOM_Site_Admin_Application_Currencies_Currencies::get($_GET['id']));
?>

<h1><?php echo osc_link_object(OSCOM::getLink(), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->getProtected('title'); ?></h3>

  <form name="cEdit" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&action=Save'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_currency'); ?></p>

  <fieldset>
    <p><label for="title"><?php echo OSCOM::getDef('field_title'); ?></label><?php echo osc_draw_input_field('title', $osC_ObjectInfo->get('title')); ?></p>
    <p><label for="code"><?php echo OSCOM::getDef('field_code'); ?></label><?php echo osc_draw_input_field('code', $osC_ObjectInfo->get('code')); ?></p>
    <p><label for="symbol_left"><?php echo OSCOM::getDef('field_symbol_left'); ?></label><?php echo osc_draw_input_field('symbol_left', $osC_ObjectInfo->get('symbol_left')); ?></p>
    <p><label for="symbol_right"><?php echo OSCOM::getDef('field_symbol_right'); ?></label><?php echo osc_draw_input_field('symbol_right', $osC_ObjectInfo->get('symbol_right')); ?></p>
    <p><label for="decimal_places"><?php echo OSCOM::getDef('field_decimal_places'); ?></label><?php echo osc_draw_input_field('decimal_places', $osC_ObjectInfo->get('decimal_places')); ?></p>
    <p><label for="value"><?php echo OSCOM::getDef('field_currency_value'); ?></label><?php echo osc_draw_input_field('value', $osC_ObjectInfo->get('value')); ?></p>

<?php
    if ( $osC_ObjectInfo->get('code') != DEFAULT_CURRENCY ) {
?>

    <p><label for="default"><?php echo OSCOM::getDef('field_set_default'); ?></label><?php echo osc_draw_checkbox_field('default'); ?></p>

<?php
    }
?>

  </fieldset>

  <p>

<?php
  if ( $osC_ObjectInfo->get('code') == DEFAULT_CURRENCY ) {
    echo osc_draw_hidden_field('is_default', 'true');
  }

  echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel')));
?>

  </p>

  </form>
</div>
