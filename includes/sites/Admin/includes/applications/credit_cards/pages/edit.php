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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_CreditCards_Admin::get($_GET['ccID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->getProtected('credit_card_name'); ?></div>
<div class="infoBoxContent">
  <form name="ccEdit" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&ccID=' . $osC_ObjectInfo->getInt('id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_card'); ?></p>

  <fieldset>
    <div><label for="credit_card_name"><?php echo $osC_Language->get('field_name'); ?></label><?php echo osc_draw_input_field('credit_card_name', $osC_ObjectInfo->get('credit_card_name')); ?></div>
    <div><label for="pattern"><?php echo $osC_Language->get('field_pattern'); ?></label><?php echo osc_draw_input_field('pattern', $osC_ObjectInfo->get('pattern')); ?></div>
    <div><label for="sort_order"><?php echo $osC_Language->get('field_sort_order'); ?></label><?php echo osc_draw_input_field('sort_order', $osC_ObjectInfo->get('sort_order')); ?></div>
    <div><label for="credit_card_status"><?php echo $osC_Language->get('field_status'); ?></label><?php echo osc_draw_checkbox_field('credit_card_status', '1', $osC_ObjectInfo->get('credit_card_status')); ?></div>
  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
