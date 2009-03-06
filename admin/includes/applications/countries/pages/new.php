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

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_country'); ?></div>
<div class="infoBoxContent">
  <form name="cNew" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_new_country'); ?></p>

  <fieldset>
    <div><label for="countries_name"><?php echo $osC_Language->get('field_name'); ?></label><?php echo osc_draw_input_field('countries_name'); ?></div>
    <div><label for="countries_iso_code_2"><?php echo $osC_Language->get('field_iso_code_2'); ?></label><?php echo osc_draw_input_field('countries_iso_code_2'); ?></div>
    <div><label for="countries_iso_code_3"><?php echo $osC_Language->get('field_iso_code_3'); ?></label><?php echo osc_draw_input_field('countries_iso_code_3'); ?></div>
    <div><label for="address_format"><?php echo $osC_Language->get('field_address_format') ; ?></label><?php echo osc_draw_textarea_field('address_format'); ?></div>
  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
