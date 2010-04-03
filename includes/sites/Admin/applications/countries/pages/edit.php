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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Countries_Admin::get($_GET['cID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->getProtected('countries_name'); ?></div>
<div class="infoBoxContent">
  <form name="cEdit" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $osC_ObjectInfo->getInt('countries_id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_country'); ?></p>

  <fieldset>
    <div><label for="countries_name"><?php echo $osC_Language->get('field_name'); ?></label><?php echo osc_draw_input_field('countries_name', $osC_ObjectInfo->get('countries_name')); ?></div>
    <div><label for="countries_iso_code_2"><?php echo $osC_Language->get('field_iso_code_2'); ?></label><?php echo osc_draw_input_field('countries_iso_code_2', $osC_ObjectInfo->get('countries_iso_code_2')); ?></div>
    <div><label for="countries_iso_code_3"><?php echo $osC_Language->get('field_iso_code_3'); ?></label><?php echo osc_draw_input_field('countries_iso_code_3', $osC_ObjectInfo->get('countries_iso_code_3')); ?></div>
    <div><label for="address_format"><?php echo $osC_Language->get('field_address_format'); ?></label><?php echo osc_draw_textarea_field('address_format', $osC_ObjectInfo->get('address_format')); ?></div>
  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
