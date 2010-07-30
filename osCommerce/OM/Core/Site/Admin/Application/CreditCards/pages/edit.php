<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\Site\Admin\Application\CreditCards\CreditCards;
  use osCommerce\OM\Core\OSCOM;

  $OSCOM_ObjectInfo = new ObjectInfo(CreditCards::get($_GET['id']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('credit_card_name'); ?></h3>

  <form name="ccEdit" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Save&Process&id=' . $OSCOM_ObjectInfo->getInt('id')); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_card'); ?></p>

  <fieldset>
    <p><label for="credit_card_name"><?php echo OSCOM::getDef('field_name'); ?></label><?php echo osc_draw_input_field('credit_card_name', $OSCOM_ObjectInfo->get('credit_card_name')); ?></p>
    <p><label for="pattern"><?php echo OSCOM::getDef('field_pattern'); ?></label><?php echo osc_draw_input_field('pattern', $OSCOM_ObjectInfo->get('pattern')); ?></p>
    <p><label for="sort_order"><?php echo OSCOM::getDef('field_sort_order'); ?></label><?php echo osc_draw_input_field('sort_order', $OSCOM_ObjectInfo->get('sort_order')); ?></p>
    <p><label for="credit_card_status"><?php echo OSCOM::getDef('field_status'); ?></label><?php echo osc_draw_checkbox_field('credit_card_status', '1', $OSCOM_ObjectInfo->get('credit_card_status')); ?></p>
  </fieldset>

  <p><?php echo osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
