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
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_country'); ?></h3>

  <form name="cNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_new_country'); ?></p>

  <fieldset>
    <p><label for="countries_name"><?php echo $osC_Language->get('field_name'); ?></label><?php echo osc_draw_input_field('countries_name'); ?></p>
    <p><label for="countries_iso_code_2"><?php echo $osC_Language->get('field_iso_code_2'); ?></label><?php echo osc_draw_input_field('countries_iso_code_2'); ?></p>
    <p><label for="countries_iso_code_3"><?php echo $osC_Language->get('field_iso_code_3'); ?></label><?php echo osc_draw_input_field('countries_iso_code_3'); ?></p>
    <p><label for="address_format"><?php echo $osC_Language->get('field_address_format') ; ?></label><?php echo osc_draw_textarea_field('address_format'); ?></p>
  </fieldset>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => $osC_Language->get('button_save'))) . ' ' . osc_draw_button(array('href' => osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), 'priority' => 'secondary', 'icon' => 'close', 'title' => $osC_Language->get('button_cancel'))); ?></p>

  </form>
</div>
