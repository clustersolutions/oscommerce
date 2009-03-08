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

  $countries_array = array(array('id' => '',
                                 'text' => $osC_Language->get('all_countries')));

  foreach ( osC_Address::getCountries() as $country ) {
    $countries_array[] = array('id' => $country['id'],
                               'text' => $country['name']);
  }

  $zones_array = array(array('id' => '',
                             'text' => $osC_Language->get('all_zones')));
?>

<script type="text/javascript"><!--
function update_zone(theForm) {
  var NumState = theForm.zone_id.options.length;
  var SelectedCountry = "";

  while(NumState > 0) {
    NumState--;
    theForm.zone_id.options[NumState] = null;
  }

  SelectedCountry = theForm.zone_country_id.options[theForm.zone_country_id.selectedIndex].value;

<?php echo osc_js_zone_list('SelectedCountry', 'theForm', 'zone_id'); ?>

}
//--></script>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_zone_entry'); ?></div>
<div class="infoBoxContent">
  <form name="zeNew" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&action=entry_save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_new_zone_entry'); ?></p>

  <fieldset>
    <div><label for="zone_country_id"><?php echo $osC_Language->get('field_country'); ?></label><?php echo osc_draw_pull_down_menu('zone_country_id', $countries_array, null, 'onchange="update_zone(this.form);"'); ?></div>
    <div><label for="zone_id"><?php echo $osC_Language->get('field_zone'); ?></label><?php echo osc_draw_pull_down_menu('zone_id', $zones_array); ?></div>
  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()]) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
