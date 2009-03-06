<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Currencies_Admin::get($_GET['cID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->getProtected('title'); ?></div>
<div class="infoBoxContent">
  <form name="cEdit" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $osC_ObjectInfo->getInt('currencies_id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_currency'); ?></p>

  <fieldset>
    <div><label for="title"><?php echo $osC_Language->get('field_title'); ?></label><?php echo osc_draw_input_field('title', $osC_ObjectInfo->get('title')); ?></div>
    <div><label for="code"><?php echo $osC_Language->get('field_code'); ?></label><?php echo osc_draw_input_field('code', $osC_ObjectInfo->get('code')); ?></div>
    <div><label for="symbol_left"><?php echo $osC_Language->get('field_symbol_left'); ?></label><?php echo osc_draw_input_field('symbol_left', $osC_ObjectInfo->get('symbol_left')); ?></div>
    <div><label for="symbol_right"><?php echo $osC_Language->get('field_symbol_right'); ?></label><?php echo osc_draw_input_field('symbol_right', $osC_ObjectInfo->get('symbol_right')); ?></div>
    <div><label for="decimal_places"><?php echo $osC_Language->get('field_decimal_places'); ?></label><?php echo osc_draw_input_field('decimal_places', $osC_ObjectInfo->get('decimal_places')); ?></div>
    <div><label for="value"><?php echo $osC_Language->get('field_currency_value'); ?></label><?php echo osc_draw_input_field('value', $osC_ObjectInfo->get('value')); ?></div>

<?php
    if ( $osC_ObjectInfo->get('code') != DEFAULT_CURRENCY ) {
?>

    <div><label for="default"><?php echo $osC_Language->get('field_set_default'); ?></label><?php echo osc_draw_checkbox_field('default'); ?></div>

<?php
    }
?>

  </fieldset>

  <p align="center">

<?php
  if ( $osC_ObjectInfo->get('code') == DEFAULT_CURRENCY ) {
    echo osc_draw_hidden_field('is_default', 'true');
  }

  echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />';
?>

  </p>

  </form>
</div>
