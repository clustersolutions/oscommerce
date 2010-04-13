<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo osc_link_object(OSCOM::getLink(), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('new.png') . ' ' . OSCOM::getDef('action_heading_new_card'); ?></h3>

  <form name="ccNew" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'action=Save'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_new_card'); ?></p>

  <fieldset>
    <p><label for="credit_card_name"><?php echo OSCOM::getDef('field_name'); ?></label><?php echo osc_draw_input_field('credit_card_name'); ?></p>
    <p><label for="pattern"><?php echo OSCOM::getDef('field_pattern'); ?></label><?php echo osc_draw_input_field('pattern'); ?></p>
    <p><label for="sort_order"><?php echo OSCOM::getDef('field_sort_order'); ?></label><?php echo osc_draw_input_field('sort_order'); ?></p>
    <p><label for="credit_card_status"><?php echo OSCOM::getDef('field_status'); ?></label><?php echo osc_draw_checkbox_field('credit_card_status', '1'); ?></p>
  </fieldset>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
