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

  $zones_array = array();

  foreach ( osc_toObjectInfo(osC_ZoneGroups_Admin::getAll(-1))->get('entries') as $group ) {
    $zones_array[] = array('id' => $group['geo_zone_id'],
                           'text' => $group['geo_zone_name']);
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_tax_rate'); ?></div>
<div class="infoBoxContent">
  <form name="trNew" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&action=entry_save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_new_tax_rate'); ?></p>

  <fieldset>
    <div><label for="tax_zone_id"><?php echo $osC_Language->get('field_tax_rate_zone_group'); ?></label><?php echo osc_draw_pull_down_menu('tax_zone_id', $zones_array); ?></div>
    <div><label for="tax_rate"><?php echo $osC_Language->get('field_tax_rate'); ?></label><?php echo osc_draw_input_field('tax_rate'); ?></div>
    <div><label for="tax_description"><?php echo $osC_Language->get('field_tax_rate_description'); ?></label><?php echo osc_draw_input_field('tax_description'); ?></div>
    <div><label for="tax_priority"><?php echo $osC_Language->get('field_tax_rate_priority'); ?></label><?php echo osc_draw_input_field('tax_priority'); ?></div>
  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()]) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
