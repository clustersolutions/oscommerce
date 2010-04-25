<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('new.png') . ' ' . OSCOM::getDef('action_heading_new_country'); ?></h3>

  <form name="cNew" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'action=Save'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_new_country'); ?></p>

  <fieldset>
    <p><label for="countries_name"><?php echo OSCOM::getDef('field_name'); ?></label><?php echo osc_draw_input_field('countries_name'); ?></p>
    <p><label for="countries_iso_code_2"><?php echo OSCOM::getDef('field_iso_code_2'); ?></label><?php echo osc_draw_input_field('countries_iso_code_2'); ?></p>
    <p><label for="countries_iso_code_3"><?php echo OSCOM::getDef('field_iso_code_3'); ?></label><?php echo osc_draw_input_field('countries_iso_code_3'); ?></p>
    <p><label for="address_format"><?php echo OSCOM::getDef('field_address_format'); ?></label><?php echo osc_draw_textarea_field('address_format'); ?><br /><i>:name</i>, <i>:street_address</i>, <i>:suburb</i>, <i>:city</i>, <i>:postcode</i>, <i>:state</i>, <i>:state_code</i>, <i>:country</i></p>
  </fieldset>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
